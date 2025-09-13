<?php

namespace Softmax\Installer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class InstallationRequest extends FormRequest
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
            // License validation
            'customer_id' => 'required|string|max:255',
            'license_key' => 'required|string|max:255',
            'domain' => 'required|string|max:255|regex:/^[a-zA-Z0-9\-\.]+$/',
            
            // Database configuration
            'db_host' => 'required|string|max:255',
            'db_port' => 'nullable|integer|min:1|max:65535',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',
            
            // Application configuration
            'app_name' => 'required|string|max:255',
            'app_url' => 'required|url|max:255',
            
            // Admin user
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'admin_password' => [
                'required',
                'string',
                'confirmed',
                Password::min(config('softmax-installer.admin.min_password_length', 8))
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
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
            
            'db_host.required' => 'Database host is required.',
            'db_database.required' => 'Database name is required.',
            'db_username.required' => 'Database username is required.',
            'db_port.integer' => 'Database port must be a valid integer.',
            
            'app_name.required' => 'Application name is required.',
            'app_url.required' => 'Application URL is required.',
            'app_url.url' => 'Application URL must be a valid URL.',
            
            'admin_name.required' => 'Administrator name is required.',
            'admin_email.required' => 'Administrator email is required.',
            'admin_email.email' => 'Administrator email must be a valid email address.',
            'admin_password.required' => 'Administrator password is required.',
            'admin_password.confirmed' => 'Administrator password confirmation does not match.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->db_port === null || $this->db_port === '') {
            $this->merge([
                'db_port' => config('softmax-installer.database.default_port', '3306'),
            ]);
        }
    }
}