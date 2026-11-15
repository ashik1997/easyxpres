<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppSetupController extends Controller
{
     public function content()
    {
        $appSetup = DB::table('app_setups')->where('id', 1)->first();

        return view('admin.app_setup.content', compact('appSetup'));
    }

    public function playGame()
    {
        $appSetup = DB::table('app_setups')->where('id', 1)->first();

        return view('admin.app_setup.play_game', compact('appSetup'));
    }

    public function updatePlayGame(Request $request)
    {

    // dd($request->all());

        DB::table('app_setups')->updateOrInsert(
            ['id' => 1],
            [
                'play_game_status' => $request->play_game_status,
                'play_game_link' => $request->play_game_link,
                'play_game_content' => $request->play_game_content
            ]
        );

        return redirect()
            ->route('admin.setup.play_game')
            ->with('success', 'Play Game settings updated successfully.');


    }

      public function linkIcon()
    {
        $appSetup = DB::table('app_setups')->where('id', 1)->first();

        return view('admin.app_setup.link_icon', compact('appSetup'));
    }

     public function updateLinkIcon(Request $request)
    {

     $imagePath = null;

        if ($request->hasFile('icon')) {
            $imagePath = $request->file('icon')->store('packages', 'public');
        }

        DB::table('app_setups')->updateOrInsert(
            ['id' => 1],
            [
                'link'        => $request->link,
                 'icon'        => $imagePath
            ]
        );

        return redirect()
            ->route('admin.setup.link_icon')
            ->with('success', 'Link and Icon updated successfully.');
    }


     public function contentRegister()
    {
        $appSetup = DB::table('app_setups')->where('id', 1)->first();

        return view('admin.app_setup.content_register', compact('appSetup'));
    }

      public function notification()
    {
        $appSetup = DB::table('app_setups')->where('id', 1)->first();

        return view('admin.app_setup.notification', compact('appSetup'));
    }

     public function changePassword()
    {
        $appSetup = DB::table('admins')->where('id', 1)->first();

        return view('admin.app_setup.change_password', compact('appSetup'));
    }

    public function updatePassword(REquest $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        DB::table('admins')->where('id', 1)->update([
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect()
            ->route('admin.change.password')
            ->with('success', 'Password and Email updated successfully.');
    }

     public function update(Request $request)
    {
        $validated = $request->validate([
            'mvr_to_bdt' => ['required', 'numeric', 'gt:0'],
            'mib_bank_name' => ['required', 'string', 'max:100'],
            'mib_bank_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'mib_balance_content' => ['nullable', 'string'],
            'bml_bank_name' => ['required', 'string', 'max:100'],
            'bml_bank_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'bml_balance_content' => ['nullable', 'string'],
        ]);

        $bankSettings = [
            'add_balance_content' => $validated['mib_balance_content'] ?? null,
            'mib_bank_name' => $validated['mib_bank_name'],
            'mib_balance_content' => $validated['mib_balance_content'] ?? null,
            'bml_bank_name' => $validated['bml_bank_name'],
            'bml_balance_content' => $validated['bml_balance_content'] ?? null,
        ];

        if ($request->hasFile('mib_bank_logo')) {
            $bankSettings['mib_bank_logo'] = $request->file('mib_bank_logo')->store('bank-logos', 'public');
        }

        if ($request->hasFile('bml_bank_logo')) {
            $bankSettings['bml_bank_logo'] = $request->file('bml_bank_logo')->store('bank-logos', 'public');
        }

        DB::table('app_setups')->updateOrInsert(
            ['id' => 1],
            array_merge($bankSettings, [
                'mvr_to_bdt' => $validated['mvr_to_bdt'],
                'bkash_rate' => $request->bkash_rate,
                'bank_rate'  => $request->bank_rate,
                'india_rate' => $request->india_rate,
                'nepal_rate' => $request->nepal_rate,
            ])
        );

        return redirect()
            ->route('admin.setup.content')
            ->with('success', 'App content updated successfully.');
    }

    public function updateLicense(Request $request)
    {
        $validated = $request->validate([
            'license_number' => ['nullable', 'string', 'max:100'],
        ]);

        DB::table('app_setups')->updateOrInsert(
            ['id' => 1],
            [
                'license_number' => filled($validated['license_number'] ?? null)
                    ? trim($validated['license_number'])
                    : null,
                'updated_at' => now(),
            ]
        );

        return redirect()
            ->route('admin.setup.content')
            ->with('success', 'Dashboard licence number updated successfully.');
    }

     public function updateRegister(Request $request)
    {


        DB::table('app_setups')->updateOrInsert(
            ['id' => 1],
            [
                'registered_balance_content'        => $request->registered_balance_content,
                'register_fee'        => $request->register_fee
            ]
        );

        return redirect()
            ->route('admin.setup.content.register')
            ->with('success', 'App content updated successfully.');
    }

      public function updateNotification(Request $request)
    {


        DB::table('app_setups')->updateOrInsert(
            ['id' => 1],
            [
                'marquee'        => $request->marquee
            ]
        );

        return redirect()
            ->route('admin.notification.message')
            ->with('success', 'App Notification updated successfully.');
    }

      public function social()
    {
        $appSetup = DB::table('app_setups')->where('id', 1)->first();

        return view('admin.app_setup.social', compact('appSetup'));
    }

     public function updateSocial(Request $request)
    {


        DB::table('app_setups')->updateOrInsert(
            ['id' => 1],
            [
                'facebook'        => $request->facebook,
                'youtube'        => $request->youtube,
                'telegram'        => $request->telegram,
                'whatsapp'        => $request->whatsapp
            ]
        );

        return redirect()
            ->route('admin.setup.social')
            ->with('success', 'App Social updated successfully.');
    }
}
