<?php

namespace Softmax\Installer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ValidateLicenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|string|max:255',
            'license_key' => 'required|string|max:255',
            'domain' => 'required|string|max:255|regex:/^[a-zA-Z0-9\-\.]+$/',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer ID is required.',
            'license_key.required' => 'License key is required.',
            'domain.required' => 'Domain is required.',
            'domain.regex' => 'Domain format is invalid.',
        ];
    }
}