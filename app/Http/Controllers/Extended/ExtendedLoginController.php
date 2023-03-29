<?php

namespace App\Http\Controllers\Extended;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use \Symfony\Component\HttpFoundation\Response;

use App\Models\User;

class ExtendedLoginController extends Controller
{
    public function login(Request $request)
    {
        // credentials:認証情報
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = User::whereEmail($request->email)->first();

            $user->tokens()->delete();
            $token = $user->createToken("login:user{$user->id}")->plainTextToken;

            return response()->json(['token' => $token ], Response::HTTP_OK);
        }

        return response()->json('User Not Found.', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
