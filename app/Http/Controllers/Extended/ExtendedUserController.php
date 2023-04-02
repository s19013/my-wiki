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
        // ユーザーが存在するか確認
        $isUserExists = User::where('email','=',$request->email)
        ->where('password','=',Hash::make($request->password))
        ->exists();

        if (!$isUserExists) {return response()->json('User Not Found.', Response::HTTP_INTERNAL_SERVER_ERROR);}

        $user = User::whereEmail($request->email)->first();
        $user->tokens()->delete();
        $token = $user->createToken("login:user{$user->id}")->plainTextToken;
        return response()->json(['token' => $token ], Response::HTTP_OK);
    }

    public function logout()
    {
        Auth::guard('sanctum')->user()->tokens()->delete();
        $res =[message => 'see you'];
        return response()->json($res, Response::HTTP_OK);
    }
}
