<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RegisterSuccessfulMail;
use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
  
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:' . User::class],
            // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'register_fee' => ['required', 'numeric', 'min:100'],
            'file_upload' => 'nullable|image|max:2048',
        ]);

        $filePath = null;
        if ($request->hasFile('file_upload')) {
            $filePath = $request->file('file_upload')->store('transactions', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'register_fee' => $request->register_fee,
            'file_upload' => $filePath,
            'password' => Hash::make($request->password),
        ]);

        // Accounts Table entry creation
        Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACCT' . str_pad($user->id, 8, '0', STR_PAD_LEFT),
            'balance' => 0,
        ]);

        event(new Registered($user));

        Auth::login($user);

        Mail::to('easyxpres9@gmail.com')->send(
            new RegisterSuccessfulMail($request->name, $request->phone)
        );

        return redirect(route('dashboard', absolute: false));
    }
}
