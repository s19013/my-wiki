<?php

namespace App\Http\Controllers\Extended;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use \Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\ExtendedLoginRequest;

use App\Models\User;

class ExtendedUserController extends Controller
{
    public function login(ExtendedLoginRequest $request)
    {
        // 今は実験のためダミートークンを発行
        // return response()->json(['token' => 'dammyToken' ], Response::HTTP_OK);

        // ユーザーが存在するか確認
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password, ])) {
            // 認証に失敗した
            return response()->json([
                'messages' => [
                    'email' => null,
                    'password' => null,
                    'other' => 'パスワードかメールアドレスが間違っています｡もしくは､あなたはまだ登録していないかもしれません'
                ]
            ],500);
        }

        $user = User::whereEmail($request->email)->first();
        $user->tokens()->delete();
        $token = $user->createToken("login:user{$user->id}")->plainTextToken;
        return response()->json(['token' => $token ], Response::HTTP_OK);
    }

    public function logout()
    {
        Auth::guard('sanctum')->user()->tokens()->delete();
        $res =['message' => 'see you'];
        return response()->json($res);
    }
}
