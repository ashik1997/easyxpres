<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'decimal:0,2',
                'min:'.config('eps.minimum_amount'),
                'max:'.config('eps.maximum_amount'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Please enter the amount you want to pay through EPS.',
            'amount.numeric' => 'The payment amount must be a valid number.',
            'amount.decimal' => 'The payment amount may contain up to two decimal places.',
            'amount.min' => 'The minimum EPS payment is BDT '.number_format((float) config('eps.minimum_amount'), 2).'.',
            'amount.max' => 'The maximum EPS payment is BDT '.number_format((float) config('eps.maximum_amount'), 2).'.',
        ];
    }
}
