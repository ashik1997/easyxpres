<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaleRecharge extends Model
{
    protected $fillable = [
        'account_id',
        'mobile',
        'amount',
        'commission',
        'status',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public static function operatorNameFromMobile(?string $mobile, iterable $operators): string
    {
        $number = preg_replace('/\D+/', '', (string) $mobile) ?? '';
        $numbers = [$number];

        if (str_starts_with($number, '960')) {
            $numbers[] = substr($number, 3);
        }

        $matchedName = 'Unknown';
        $matchedCodeLength = 0;

        foreach ($operators as $operator) {
            $code = preg_replace('/\D+/', '', (string) $operator->code) ?? '';

            if ($code === '' || strlen($code) <= $matchedCodeLength) {
                continue;
            }

            foreach ($numbers as $candidate) {
                if (str_starts_with($candidate, $code)) {
                    $matchedName = (string) $operator->name;
                    $matchedCodeLength = strlen($code);
                    break;
                }
            }
        }

        return $matchedName;
    }
}
