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
        if (Auth::attempt($request->only('email', 'password'), $request->remember_me)) {
            $user = Auth::user();

            $token = $user->createToken('auth')->accessToken;

            return $this->success('Welcome back, ' . $user->last_name . '!', ['access_token' => $token, 'user' => $user]);
        }

        return $this->error('User credentials are invalid');
    }

    public function register(RegisterRequest $request)
    {
        User::create($request->validated());
        return $this->success('Account created successfully!');
    }

    public function update(RegisterRequest $request)
    {

        if ($request->hasFile('image')) {
            $fileName = 'image'.time().'.'.$request->image->extension();
            auth()->user()->update(['photo' => $fileName]);
            $request->file('image')->move(public_path('images/profile'), $fileName);
        }

        if(!$request->photo) {
            auth()->user()->update(['photo' => null]);
        }

        auth()->user()->update($request->validated());
        return $this->success('Account information has been updated successfully!');
    }

    public function authUser()
    {
        return $this->success('User authenticated', ['user' => auth('api')->user()]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->success('User logged out successfully!');
    }
}
