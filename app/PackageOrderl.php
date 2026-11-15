<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageOrderl extends Model
{
    protected $fillable = [
        'package_id',
        'account_id',
        'items',
        'number',
        'amount',
        'status',
    ];

    public function package()
    {
        return $this->belongsTo(PackageDetail::class);
    }

     public function account()
    {
        return $this->belongsTo(Account::class);
    }
    
}
