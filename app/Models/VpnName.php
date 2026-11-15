<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VpnName extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function vpns(): HasMany
    {
        return $this->hasMany(Vpn::class);
    }
}
