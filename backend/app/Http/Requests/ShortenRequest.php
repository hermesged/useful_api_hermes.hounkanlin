<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShortenRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'original_url' => 'required|url',
            'custom_code' => 'nullable|string|max:10|regex:/^[A-Za-z0-9_-]+$/|unique:short_links,code',
        ];
    }
}