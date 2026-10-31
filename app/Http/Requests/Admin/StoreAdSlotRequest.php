<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdSlotRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'position' => ['required', 'string', 'in:header,above_timer,below_timer,footer'],
            'ad_code' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'device_target' => ['required', 'string', 'in:all,desktop,mobile'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:255'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}

