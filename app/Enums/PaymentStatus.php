<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Successful = 'successful';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Processing => 'Processing',
            self::Successful => 'Successful',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Expired',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-500/15 text-amber-300 border-amber-500/30',
            self::Processing => 'bg-blue-500/15 text-blue-300 border-blue-500/30',
            self::Successful => 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
            self::Failed => 'bg-red-500/15 text-red-300 border-red-500/30',
            self::Cancelled => 'bg-slate-500/15 text-slate-300 border-slate-500/30',
            self::Expired => 'bg-purple-500/15 text-purple-300 border-purple-500/30',
        };
    }
}
