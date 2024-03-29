<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            "id"   => 'integer',
            "name" => "required|max:255",
        ];
    }

    public function messages()
    {
        try {
            if ((substr($this->headers->get("UserLang"), 0,2)) == 'ja'){
                return [
                    "name.required" => "新しいタグ名を入力してください",
                    "name.max"      => "126文字以内で入力してください"
                ];
            }
        } catch (\Throwable $th) {}
        return [
            "name.required" => "Enter new tag name",
            "name.max"      => "Please enter within 255 characters"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $res = response()->json([
            'messages' => $validator->errors(),
            ],
            400);
        throw new HttpResponseException($res);
    }
}

