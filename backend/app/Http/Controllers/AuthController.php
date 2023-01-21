<?php

namespace App\Http\Controllers;

use App\Http\Requests\auth\LoginRequest;
use App\Http\Requests\auth\RegisterRequest;
use App\Mail\AccountStatus;
use App\Mail\EmailVerification;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use ApiResponse;

    public function login(LoginRequest $request)
    {
        $userVerified = User::whereEmail($request->email)->whereNotNull('email_verified_at')->first();

        if (!$userVerified) {
            return $this->error('Your email address is not yet verified');
        }

        $user = User::whereEmail($request->email)->whereNotNull('approved_at')->first();

        if (!$user) {
            if ($user && $user->position == 'Assistant-Coach') return;
            return $this->error('Account has either been declined or not approved');
        }

        if (Auth::attempt($request->only('email', 'password'), $request->remember_me)) {
            $user = User::find(auth()->id());
            $user->load(['players', 'drills']);

            $token = $user->createToken('auth')->accessToken;

            return $this->success('Welcome back, ' . $user->last_name . '!', ['access_token' => $token, 'user' => $user]);
        }

        return $this->error('User credentials are invalid');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        Mail::to($user->email)->send(new EmailVerification($user));

        return $this->success('Account created successfully!');
    }

    public function update(RegisterRequest $request)
    {

        if ($request->hasFile('image')) {
            $fileName = 'image' . time() . '.' . $request->image->extension();
            auth()->user()->update(['photo' => $fileName]);
            $request->file('image')->move(public_path('images/profile'), $fileName);
        }

        if (!$request->photo) {
            auth()->user()->update(['photo' => null]);
        }

        auth()->user()->update($request->validated());
        return $this->success('Account information has been updated successfully!');
    }

    public function verify(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->error('Email doesn\'t exists');
        }

        if ($user && $user->email_verified_at) {
            return $this->error('Email has been verified already!');
        } else {
            $user->update([
                'email_verified_at' => now()
            ]);

            return $this->success('Account has been verified!');
        }
    }

    public function approve(User $user)
    {
        $user->update([
            'approved_at' => now(),
            'declined_at' => null
        ]);

        Mail::to($user->email)->send(new AccountStatus($user, $status = 'Approved'));

        return $this->success('Athlete\'s account has been approved!');
    }

    public function decline(User $user)
    {
        $user->update([
            'approved_at' => null,
            'declined_at' => now()
        ]);

        Mail::to($user->email)->send(new AccountStatus($user, $status = 'Declined'));

        return $this->success('Athlete\'s account has been declined!');
    }

    public function authUser()
    {
        $user = auth('api')->user();

        return $this->success('User authenticated', ['user' => $user]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return $this->success('User logged out successfully!');
    }
}
