<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class appSetup extends Model
{
     protected $fillable = [
        'add_balance_content',
        'mib_bank_name',
        'mib_bank_logo',
        'mib_balance_content',
        'bml_bank_name',
        'bml_bank_logo',
        'bml_balance_content',
        'registered_balance_content',
        'icon',
        'license_number',
        'link',
        'register_fee',
        'facebook',
        'youtube',
        'telegram',
        'marquee',
        'play_game_status',
        'play_game_link',
        'play_game_content'
    ];
}
