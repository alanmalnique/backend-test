<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class DepositRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'funds' => 'required|numeric|min:0.01'
        ];
    }
}
