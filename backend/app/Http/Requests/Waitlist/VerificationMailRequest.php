<?php

namespace App\Http\Requests\Waitlist;

use Illuminate\Foundation\Http\FormRequest;

class VerificationMailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'token' => 'required|string|max:255|',
            'mail' => 'required|email|max:50',
        ];
    }

    public function wantsJson(): true
    {
        return true;
    }

}
