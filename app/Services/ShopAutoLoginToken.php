<?php

namespace App\Services;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Throwable;

class ShopAutoLoginToken
{
    private const CIPHER = 'AES-256-CBC';

    public function issue(string $phone, ?string $name = null, ?string $email = null): string
    {
        $phone = preg_replace('/\s+/', '', trim($phone)) ?? '';

        if (! preg_match('/^\+?[0-9]{7,20}$/', $phone)) {
            throw new InvalidArgumentException('A valid phone number is required.');
        }

        $name = is_string($name) && trim($name) !== ''
            ? mb_substr(trim($name), 0, 255)
            : null;
        $email = is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL)
            ? mb_strtolower(mb_substr(trim($email), 0, 255))
            : null;

        $issuedAt = now()->timestamp;
        $payload = [
            'v' => 1,
            'iss' => $this->issuer(),
            'aud' => $this->audience(),
            'phone' => $phone,
            'name' => $name,
            'email' => $email,
            'jti' => bin2hex(random_bytes(32)),
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->ttl(),
        ];

        try {
            return $this->encrypter()->encryptString(json_encode($payload, JSON_THROW_ON_ERROR));
        } catch (JsonException $exception) {
            throw new RuntimeException('The shop auto-login token could not be created.', previous: $exception);
        }
    }

    /** @return array{phone: string, name: ?string, email: ?string, jti: string, iat: int, exp: int} */
    public function consume(string $token): array
    {
        if ($token === '' || strlen($token) > 8192) {
            throw new InvalidArgumentException('The shop auto-login token is invalid.');
        }

        try {
            $payload = json_decode($this->encrypter()->decryptString($token), true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new InvalidArgumentException('The shop auto-login token is invalid.', previous: $exception);
        }

        if (! is_array($payload) || ! $this->hasValidClaims($payload)) {
            throw new InvalidArgumentException('The shop auto-login token is invalid or expired.');
        }

        $cacheKey = 'shop-auto-login:used:'.hash('sha256', $payload['jti']);
        $remainingLifetime = max(1, $payload['exp'] - now()->timestamp);
        $store = Cache::store(config('shop_auto_login.cache_store'));

        if (! $store->add($cacheKey, true, $remainingLifetime)) {
            throw new InvalidArgumentException('The shop auto-login token has already been used.');
        }

        return [
            'phone' => $payload['phone'],
            'name' => $payload['name'] ?? null,
            'email' => $payload['email'] ?? null,
            'jti' => $payload['jti'],
            'iat' => $payload['iat'],
            'exp' => $payload['exp'],
        ];
    }

    /** @param array<string, mixed> $payload */
    private function hasValidClaims(array $payload): bool
    {
        if (($payload['v'] ?? null) !== 1
            || ! is_string($payload['iss'] ?? null)
            || ! is_string($payload['aud'] ?? null)
            || ! is_string($payload['phone'] ?? null)
            || ! is_string($payload['jti'] ?? null)
            || ! is_int($payload['iat'] ?? null)
            || ! is_int($payload['exp'] ?? null)
            || ! $this->hasValidOptionalIdentity($payload)) {
            return false;
        }

        $now = now()->timestamp;
        $clockSkew = max(0, (int) config('shop_auto_login.clock_skew_seconds', 5));

        return hash_equals($this->issuer(), $payload['iss'])
            && hash_equals($this->audience(), $payload['aud'])
            && preg_match('/^\+?[0-9]{7,20}$/', $payload['phone']) === 1
            && preg_match('/^[a-f0-9]{64}$/', $payload['jti']) === 1
            && $payload['iat'] <= $now + $clockSkew
            && $payload['exp'] > $now
            && $payload['exp'] > $payload['iat']
            && ($payload['exp'] - $payload['iat']) <= $this->ttl();
    }

    /** @param array<string, mixed> $payload */
    private function hasValidOptionalIdentity(array $payload): bool
    {
        $name = $payload['name'] ?? null;
        $email = $payload['email'] ?? null;

        return ($name === null || (is_string($name) && mb_strlen($name) <= 255))
            && ($email === null || (is_string($email)
                && strlen($email) <= 255
                && filter_var($email, FILTER_VALIDATE_EMAIL)));
    }

    private function encrypter(): Encrypter
    {
        $configuredKey = (string) config('shop_auto_login.key', '');
        $key = str_starts_with($configuredKey, 'base64:')
            ? base64_decode(substr($configuredKey, 7), true)
            : $configuredKey;

        if (! is_string($key) || strlen($key) !== 32) {
            throw new RuntimeException('SHOP_AUTO_LOGIN_KEY must contain a base64-encoded 32-byte key.');
        }

        return new Encrypter($key, self::CIPHER);
    }

    private function ttl(): int
    {
        return max(15, min(300, (int) config('shop_auto_login.ttl_seconds', 60)));
    }

    private function issuer(): string
    {
        return (string) config('shop_auto_login.issuer', 'easyxpress');
    }

    private function audience(): string
    {
        return (string) config('shop_auto_login.audience', 'ecom-shop');
    }
}
