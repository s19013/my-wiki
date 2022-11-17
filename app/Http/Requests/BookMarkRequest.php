<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BookMarkRequest extends FormRequest
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
            "bookMarkTitle" => "max:255",
            "bookMarkUrl"   => "required|url"
        ];
    }

    public function messages()
    {
        return [
            "bookMarkTitle.max"    => "126文字以内で入力してください",
            "bookMarkUrl.required" => "urlを入力してください",
            "bookMarkUrl.url"      => "url形式で入力してください"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $res = response()->json([
            'errors' => $validator->errors(),
            ],
            400);
        throw new HttpResponseException($res);
    }
}

