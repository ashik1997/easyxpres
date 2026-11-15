<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BalancePaymentTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'account_id',
        'ledger_transaction_id',
        'transaction_id',
        'gateway',
        'gateway_transaction_id',
        'gateway_session_id',
        'amount',
        'currency',
        'balance_amount',
        'balance_currency',
        'exchange_rate',
        'status',
        'merchant_id_hash',
        'store_id_hash',
        'callback_token_hash',
        'request_payload',
        'response_payload',
        'status_log',
        'callback_count',
        'last_callback_at',
        'verified_at',
        'failed_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_amount' => 'decimal:2',
            'exchange_rate' => 'decimal:6',
            'status' => PaymentStatus::class,
            'request_payload' => 'array',
            'response_payload' => 'array',
            'status_log' => 'array',
            'last_callback_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'transaction_id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function ledgerTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'ledger_transaction_id');
    }
}
