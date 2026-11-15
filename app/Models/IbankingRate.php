<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IbankingRate extends Model
{
    protected $fillable = [
    'rate',
    'status',
];
}
