<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ArticleRequest extends FormRequest
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
        // integerにすると数値型に変換してくれるらしい
        return [
            "articleId"    => 'integer',
            "articleTitle" => "max:255",
            "articleBody"  => "",
            "tagList"      => "array",
        ];
    }

    public function messages()
    {
        return [
            "articleTitle.max"    => "126文字以内で入力してください",
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

