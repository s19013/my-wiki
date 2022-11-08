<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Validation\Validator;

class TagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {return true;}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "tag" => "required|max:255",
        ];
    }

    public function messages()
    {
        return [
            "tag.required" => "新しいタグ名を入力してください",
            "tag.max"      => "126文字以内で入力してください"
        ];
    }
}
