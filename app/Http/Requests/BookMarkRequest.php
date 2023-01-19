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
            "bookMarkId"    => 'integer',
            "bookMarkTitle" => "max:255",
            "bookMarkUrl"   => "required|url",
            "tagList"       => "array",
        ];
    }

    public function messages()
    {
        if ((substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0,2)) == 'ja') {
            return [
                "bookMarkTitle.max"    => "126文字以内で入力してください",
                "bookMarkUrl.required" => "urlを入力してください",
                "bookMarkUrl.url"      => "url形式で入力してください"
            ];
        }
        return [
            "bookMarkTitle.max"    => "Please enter within 255 characters",
            "bookMarkUrl.required" => "please enter the url",
            "bookMarkUrl.url"      => "Please enter in url format"
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

