<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminForgotPasswordController extends Controller
{
    public function showForm()
    {
        return view('admin.auth.forgot-password');
    }

     public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()->with('error', 'Email not found!');
        }

        $otp = rand(100000, 999999);

        $admin->update([
            'otp' => $otp,
            'otp_expire_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::raw("Your OTP is: $otp", function ($message) use ($admin) {
            $message->to($admin->email)
                ->subject('Admin Password Reset OTP');
        });

        session(['reset_email' => $admin->email]);

        return redirect()->route('admin.verify.form')->with('success', 'OTP sent to email.');
    }

    public function verifyForm()
    {
        return view('admin.auth.verify-otp');
    }

     public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);

        $admin = Admin::where('email', session('reset_email'))
            ->where('otp', $request->otp)
            ->where('otp_expire_at', '>', Carbon::now())
            ->first();

        if (!$admin) {
            return back()->with('error', 'Invalid or Expired OTP');
        }

        return redirect()->route('admin.reset.form');
    }

     public function resetForm()
    {
        return view('admin.auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $admin = Admin::where('email', session('reset_email'))->first();

        $admin->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expire_at' => null,
        ]);

        return redirect()->route('admin.login')->with('success', 'Password reset successful.');
    }

}
