<?php

namespace App\Http\Requests\Saas;

use Illuminate\Foundation\Http\FormRequest;

class OpenAiRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => 'required|string|max:255',
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d',
        ];
    }

    public function wantsJson(): true
    {
        return true;
    }

}
