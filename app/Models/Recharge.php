<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
     protected $fillable = [
        'account_id',
        'mobile',
        'amount',
        'status',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getOperatorNameAttribute(): string
    {
        return self::operatorFromMobile($this->mobile);
    }

    public static function operatorFromMobile(?string $mobile): string
    {
        $number = preg_replace('/\D+/', '', (string) $mobile) ?? '';

        if (str_starts_with($number, '880')) {
            $number = '0'.substr($number, 3);
        } elseif (strlen($number) === 10 && str_starts_with($number, '1')) {
            $number = '0'.$number;
        }

        return match (substr($number, 0, 3)) {
            '013', '017' => 'Grameenphone',
            '014', '019' => 'Banglalink',
            '015' => 'Teletalk',
            '016' => 'Airtel',
            '018' => 'Robi',
            default => 'Unknown',
        };
    }
}
