<?php

namespace Softmax\Installer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestDatabaseRequest extends FormRequest
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
            'host' => 'required|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'database' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'host.required' => 'Database host is required.',
            'database.required' => 'Database name is required.',
            'username.required' => 'Database username is required.',
            'port.integer' => 'Port must be a valid integer.',
            'port.min' => 'Port must be at least 1.',
            'port.max' => 'Port must not exceed 65535.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->port === null || $this->port === '') {
            $this->merge([
                'port' => config('softmax-installer.database.default_port', '3306'),
            ]);
        }
    }
}