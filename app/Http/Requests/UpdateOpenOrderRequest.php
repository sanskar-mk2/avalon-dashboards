<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOpenOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ext_sales' => ['nullable', 'numeric'],
            'ext_cost' => ['nullable', 'numeric'],
        ];
    }
} 