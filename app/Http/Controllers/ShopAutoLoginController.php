<?php

namespace App\Http\Controllers;

use App\Services\ShopAutoLoginToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class ShopAutoLoginController extends Controller
{
    public function __invoke(Request $request, ShopAutoLoginToken $tokens): RedirectResponse
    {
        $phone = $request->user()?->phone;

        if (! is_string($phone) || trim($phone) === '') {
            return redirect()->route('dashboard')->with('error', 'A valid phone number is required before opening Shop.');
        }

        $targetUrl = (string) config('shop_auto_login.target_url');

        if (! filter_var($targetUrl, FILTER_VALIDATE_URL)
            || ! in_array(parse_url($targetUrl, PHP_URL_SCHEME), ['http', 'https'], true)) {
            report(new RuntimeException('SHOP_AUTO_LOGIN_URL is not configured correctly.'));

            return redirect()->route('dashboard')->with('error', 'Secure Shop login is temporarily unavailable. Please try again later.');
        }

        try {
            $token = $tokens->issue($phone, $request->user()?->name, $request->user()?->email);
        } catch (InvalidArgumentException) {
            return redirect()->route('dashboard')->with('error', 'A valid phone number is required before opening Shop.');
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('dashboard')->with('error', 'Secure Shop login is temporarily unavailable. Please try again later.');
        }

        $separator = str_contains($targetUrl, '?') ? '&' : '?';
        $handoffUrl = $targetUrl.$separator.http_build_query(
            ['token' => $token],
            '',
            '&',
            PHP_QUERY_RFC3986
        );

        return redirect()
            ->away($handoffUrl)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Referrer-Policy', 'no-referrer');
    }
}
