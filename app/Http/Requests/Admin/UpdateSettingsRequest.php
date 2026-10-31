<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'site_name' => ['required', 'string', 'max:100'],
            'default_countdown_seconds' => ['required', 'integer', 'min:5', 'max:60'],
            'turnstile_mode' => ['required', 'string', 'in:enabled,test,bypass'],
            'session_ttl_minutes' => ['required', 'integer', 'min:5', 'max:120'],
            'adsterra_enabled' => ['required', 'boolean'],
        ];
    }
}

