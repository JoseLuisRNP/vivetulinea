<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'weight' => 'required|numeric|min:20|max:300|decimal:0,2',
            'date' => 'required|date|before_or_equal:today',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'weight.min' => 'El peso debe ser al menos :min kg.',
            'weight.max' => 'El peso no puede superar :max kg.',
            'weight.decimal' => 'El peso admite como máximo 2 decimales.',
            'weight.numeric' => 'El peso debe ser un número.',
            'date.before_or_equal' => 'La fecha no puede ser futura.',
        ];
    }
}
