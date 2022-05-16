<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{
    const EMAIL_TO_ENDPOINTS = 'xpto@drconsulta.dev';
    const PASSWORD_TO_ENDPOINTS = 'xpto@D$K0$';

    public function login(Request $request)
    {
        $validate = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $isAuth = $validate['email'] == self::EMAIL_TO_ENDPOINTS && $validate['password'] == self::PASSWORD_TO_ENDPOINTS;

        if ($isAuth) {
            return response()->json(['token' => md5($validate['email'] . $validate['password'] . 'api-users'), 'message' => 'User logged'], 200);
        }

        return response()->json(['message' => 'User not found'], 401);
    }
}