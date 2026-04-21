<?php

namespace App\Http\Requests\SiteContent;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteContentRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'about.ru' => ['required', 'string', 'max:2000'],
            'about.tk' => ['required', 'string', 'max:2000'],
            'about_title.ru' => ['required', 'string', 'max:255'],
            'about_title.tk' => ['required', 'string', 'max:255'],
        ];
    }
}
