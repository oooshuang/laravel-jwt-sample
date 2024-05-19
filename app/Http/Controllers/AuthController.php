<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


/**
 * @OA\Info(
 *     title="API",
 *     version="1.0.1",
 *     description="Laravel Sample API Documentation",
 *     @OA\Contact(
 *         email="hrbhhot@gamil.com"
 *     ),
 *     @OA\License(
 *         name="Private License",
 *         url="https://github.com/oooshuang/laravel-jwt-sample"
 *     )
 * )
 */
class AuthController extends Controller
{
    // 生成 swagger文档
    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     operationId="register",
     *     @OA\RequestBody(
     *         description="User data",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"name": "John Doe", "email": "john@example.com", "password": "password123"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 example={"name": "John Doe", "email": "john@example.com"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="message",
     *                     type="string"
     *                 ),
     *                 example={"message": "The given data was invalid."}
     *             )
     *         )
     *     )
     * )
     */
    public function register()
    {
        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = new User;
        $user->name = request()->name;
        $user->email = request()->email;
        $user->password = bcrypt(request()->password);
        $user->save();

        return response()->json($user, 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Authentication"},
     *     summary="Log in a user",
     *     operationId="login",
     *     @OA\RequestBody(
     *         description="User credentials",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"email": "john@example.com", "password": "password123"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="access_token",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="token_type",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="expires_in",
     *                     type="integer"
     *                 ),
     *                 example={"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...", "token_type": "bearer", "expires_in": 31536000}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="error",
     *                     type="string"
     *                 ),
     *                 example={"error": "Unauthorized"}
     *             )
     *         )
     *     )
     * )
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/me",
     *     tags={"Authentication"},
     *     summary="Get current user",
     *     operationId="me",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User information",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 example={"id": 1, "name": "John Doe", "email": "john@example.com"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="message",
     *                     type="string"
     *                 ),
     *                 example={"message": "Unauthenticated."}
     *             )
     *         )
     *     )
     * )
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     tags={"Authentication"},
     *     summary="Log out a user",
     *     operationId="logout",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="message",
     *                     type="string"
     *                 ),
     *                 example={"message": "Successfully logged out"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="message",
     *                     type="string"
     *                 ),
     *                 example={"message": "Unauthenticated."}
     *             )
     *         )
     *     )
     * )
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh a token",
     *     operationId="refresh",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="access_token",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="token_type",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="expires_in",
     *                     type="integer"
     *                 ),
     *                 example={"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...", "token_type": "bearer", "expires_in": 31536000}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="message",
     *                     type="string"
     *                 ),
     *                 example={"message": "Unauthenticated."}
     *             )
     *         )
     *     )
     * )
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }



    private function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60 * 24 * 365
        ]);
    }


}
