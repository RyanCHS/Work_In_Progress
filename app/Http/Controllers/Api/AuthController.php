<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\Validator;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="API untuk mengelola Auth termasuk login dan logout"
 * )
 */
class AuthController extends Controller
{
  protected UserServiceInterface $userService;

  public function __construct(UserServiceInterface $userService)
  {
      $this->userService = $userService;
  }

  /**
   * @OA\Post(
   *     path="/api/register",
   *     tags={"Auth"},
   *     summary="Register a new user",
   *     operationId="registerUser",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"Username","Password","UserGroup","Name"},
   *             @OA\Property(property="Username", type="string", example="john.doe"),
   *             @OA\Property(property="Password", type="string", example="secret123"),
   *             @OA\Property(property="UserGroup", type="string", example="admin"),
   *             @OA\Property(property="Name", type="string", example="John Doe"),
   *             @OA\Property(property="IsActivated", type="boolean", example="false")
   *         )
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="User created successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="User created successfully"),
   *             @OA\Property(
   *                 property="user",
   *                 type="object",
   *                 @OA\Property(property="UserId", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
   *                 @OA\Property(property="Username", type="string", example="john.doe"),
   *                 @OA\Property(property="UserGroup", type="string", example="admin"),
   *                 @OA\Property(property="IsActivated", type="boolean", example=true)
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation Error"
   *     )
   * )
   */
  public function register(Request $request)
  {
    $validated = $request->validate([
      'Username'    => 'required|string|unique:User,Username',
      'Password'    => 'required|string|min:6',
      'UserGroup'   => 'required|string',
      'Name'        => 'required|string',
      'IsActivated' => 'required|boolean',
    ]);

    return $this->userService->createUser($validated);
  }

  /**
   * @OA\Post(
   *     path="/api/auth/login",
   *     tags={"Auth"},
   *     summary="Login user dengan username dan password",
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"Username","Password"},
   *             @OA\Property(property="Username", type="string", example="bhuedies"),
   *             @OA\Property(property="Password", type="string", example="secret123")
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Login berhasil",
   *         @OA\JsonContent(
   *             @OA\Property(property="access_token", type="string"),
   *             @OA\Property(property="token_type", type="string", example="bearer"),
   *             @OA\Property(property="expires_in", type="integer")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthorized"
   *     )
   * )
   */
  public function login(Request $request)
  {
    return $this->userService->login($request);
  }

  /**
   * @OA\Post(
   *     path="/api/auth/logout",
   *     summary="Logout pengguna",
   *     tags={"Auth"},
   *     @OA\Response(
   *         response=200,
   *         description="Berhasil logout",
   *         @OA\JsonContent(
   *             type="object",
   *             @OA\Property(property="message", type="string", example="Berhasil logout")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthorized"
   *     )
   * )
   */
  public function logout()
  {
    return $this->userService->logout();
  }

  /**
   * @OA\Get(
   *     path="/api/auth/me",
   *     summary="Get authenticated user",
   *     tags={"Auth"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Response(
   *         response=200,
   *         description="Successful response",
   *         @OA\JsonContent(
   *             type="object",
   *             @OA\Property(property="UserId", type="string", format="uuid"),
   *             @OA\Property(property="Username", type="string"),
   *             @OA\Property(property="UserGroup", type="string")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthorized"
   *     )
   * )
   */
  public function me()
  {
    return $this->userService->me();
  }

    /**
   * @OA\Post(
   *     path="/auth/refresh",
   *     summary="Refresh token JWT",
   *     tags={"Auth"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Response(
   *         response=200,
   *         description="Token berhasil diperbarui",
   *         @OA\JsonContent(
   *             @OA\Property(property="access_token", type="string"),
   *             @OA\Property(property="token_type", type="string"),
   *             @OA\Property(property="expires_in", type="integer")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthorized",
   *         @OA\JsonContent(
   *             @OA\Property(property="error", type="string", example="Unauthorized")
   *         )
   *     )
   * )
   */
  public function refresh()
  {
    $newToken = JWTAuth::refresh(JWTAuth::getToken());

    return response()->json([
        'access_token' => $newToken,
        'token_type' => 'bearer',
        'expires_in' => auth()->factory()->getTTL() * 60
    ]);
}
}
