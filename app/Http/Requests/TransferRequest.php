<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'from' => 'required|exists:customers,id',
            'to' => 'required|different:from|exists:customers,id',
            'funds' => 'required|numeric|min:0.01',
        ];
    }
}
