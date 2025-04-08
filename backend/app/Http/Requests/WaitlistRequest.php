<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WaitlistRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:70',
            'name' => 'required|string|max:50',
        ];
    }

    public function wantsJson(): true
    {
        return true; // Force toujours JSON
    }

}
