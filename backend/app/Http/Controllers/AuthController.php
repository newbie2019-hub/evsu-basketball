<?php

namespace App\Http\Controllers;

use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            $token = $user->createToken('auth')->accessToken;

            return $this->success('User authenticated successfully!', ['access_token' => $token, 'user' => $user]);
        }

        return $this->error('User credentials are invalid');
    }

    public function register(RegisterRequest $request)
    {
        User::create($request->validated());
        return $this->success('Account created successfully!');
    }

    public function authUser()
    {
        return $this->success('User authenticated', ['user' => auth('api')->user()]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        // auth('api')->logout();

        return $this->success('User logged out successfully!');
    }
}
