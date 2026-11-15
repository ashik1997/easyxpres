<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileBankingOrder extends Model
{

protected $fillable = [
        'account_id',
        'mobile_banking_id',
        'number',
        'amount',
        'bdt_amount',
        'note_admin',
        'money_status',
        'status',
    ];
      public function account()
    {
        return $this->belongsTo(Account::class);
    }
   public function mobile()
    {
        return $this->belongsTo(MobileBanking::class, 'mobile_banking_id');
    }
    
}
