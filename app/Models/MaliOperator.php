<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaliOperator extends Model
{
    protected $fillable = [
        'name',
        'code',
        'commission',
        'status',
    ];

    protected $casts = [
        'commission' => 'decimal:2',
    ];
}
