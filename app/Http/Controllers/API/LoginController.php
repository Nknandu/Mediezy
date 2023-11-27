<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class LoginController extends BaseController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, true)) {
            // Authentication successful
            if (Auth::user()->user_role == '1') {
                return response(['message' => 'Login successful.', 'user' => Auth::user()], 200);
            } else {
                return response(['message' => 'Login successful.', 'user' => Auth::user()], 200);
            }
        }

        // Authentication failed
        return response(['message' => 'The provided credentials do not match our records.'], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response(['message' => 'You have been successfully logged out!'], 200);
    }
}
