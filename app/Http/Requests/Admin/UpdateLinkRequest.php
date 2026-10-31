<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Services\SlugGeneratorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLinkRequest extends FormRequest
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
        $link = $this->route('link');
        $linkId = is_object($link) ? $link->id : $link;

        return [
            'destination_url' => [
                'required',
                'string',
                'url:http,https',
                'max:2048',
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'alpha_dash:ascii',
                'min:3',
                'max:50',
                Rule::unique('links', 'slug')->ignore($linkId),
                Rule::notIn(SlugGeneratorService::RESERVED_SLUGS),
            ],
            'source_website' => ['nullable', 'string', 'max:255'],
            'countdown_seconds' => ['required', 'integer', 'min:5', 'max:60'],
            'expires_at' => ['nullable', 'date'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
}

