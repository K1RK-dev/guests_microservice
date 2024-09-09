<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(StoreUserRequest $request) {
        return User::create($request->all());
    }

    public function login(LoginUserRequest $request) {
        if(!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Wrong email or password!'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = User::query()->where('email', $request->email)->first();
        $user->tokens()->delete();
        return response()->json([
            'user' => $user,
            'token' => $user->createToken("Token of {$user->name}")->plainTextToken
        ]);
    }

    public function logout() {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Token removed'
        ]);
    }
}
