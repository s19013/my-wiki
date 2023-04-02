<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExtendedLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){return true;}

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required'
        ];
    }

    public function messages()
    {
        try {
            if ((substr($this->headers->get("UserLang"), 0,2)) == 'ja'){
                return [
                    "email.required"   => "メールアドレスを入力してください",
                    "email.email"      => "メールアドレスを入力してください",
                    "password.required"=> "パスワードを入力してください"
                ];
            }
        } catch (\Throwable $th) {}
        return [
            "email.required"   => "Please enter email",
            "email.email"      => "Please enter email",
            "password.required"=> "Please enter password",
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
