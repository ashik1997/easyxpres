<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'image_icon',
        'name',
        'status',
    ];

    public function details()
    {
        return $this->hasMany(PackageDetail::class);
    }

    public function orders()
    {
        return $this->hasMany(PackageOrderl::class);
    }

    
    
}
