<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageDetail extends Model
{
    protected $fillable = [
        'package_id',
        'title',
        'amount',
        'commission',
        'offer_price',
        'status',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    
}
