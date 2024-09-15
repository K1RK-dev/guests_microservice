<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    /**
     * @OA\Post(path="/api/v1/register", summary="Регистрация пользователя", description="Создаёт пользователя, с доступом к API.", tags={"Auth"},
     *     @OA\Parameter(name="name", in="query", description="Имя пользователя", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="email", in="query", description="Электронная почта пользователя", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="password", in="query", description="Пароль для пользователя", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=201, description="Ответ содержит имя пользователя, электронную почту и уникальный идентификатор",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="id", type="integer"),
     *          )
     *     )
     * )
     *
     * @param StoreUserRequest $request
     * @return mixed
     */
    public function register(StoreUserRequest $request) {
        return User::create($request->all());
    }

    /**
     * @OA\Post(path="/api/v1/login", summary="Авторизация пользователя", description="Авторизация и получения токена для использования API.", tags={"Auth"},
     *     @OA\Parameter(name="email", in="query", description="Электронная почта пользователя", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(name="password", in="query", description="Пароль пользователя", required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=201, description="Ответ содержит данные о пользователе и токен для доступа к API.",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="user", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="email", type="string"),
     *              ),
     *              @OA\Property(property="token", type="string"),
     *          )
     *     )
     * )
     *
     * @param LoginUserRequest $request
     * @return JsonResponse
     */
    public function login(LoginUserRequest $request) {
        if(!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Wrong email or password!'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = User::query()->where('email', $request->email)->first();
        $user->tokens()->delete();
        $user->makeHidden(['email_verified_at']);
        return response()->json([
            'user' => $user,
            'token' => $user->createToken("Token of {$user->name}")->plainTextToken
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/logout",
     *     summary="Деаунтификация пользователя",
     *     description="Удаляет токен пользователя(доступ к API).",
     *     tags={"Auth"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *          response=200,
     *          description="Successful response",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="message", type="string", example="Token removed")
     *          )
     *     )
     * )
     *
     * @return JsonResponse
     */
    public function logout() {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Token removed'
        ]);
    }
}
