<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // all authenticated users can create videos
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "soundtrack_id" => "required|exists:soundtracks,id",
            "images" => "required|array|min:5|max:10",
            "images.*" => "required|image",
            "webhook" => "required|url",
        ];
    }
}
