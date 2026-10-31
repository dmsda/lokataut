<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Services\SlugGeneratorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLinkRequest extends FormRequest
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
            'destination_url' => [
                'required',
                'string',
                'url:http,https',
                'max:2048',
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'alpha_dash:ascii',
                'min:3',
                'max:50',
                'unique:links,slug',
                Rule::notIn(SlugGeneratorService::RESERVED_SLUGS),
            ],
            'source_website' => ['nullable', 'string', 'max:255'],
            'countdown_seconds' => ['nullable', 'integer', 'min:5', 'max:60'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }
}

