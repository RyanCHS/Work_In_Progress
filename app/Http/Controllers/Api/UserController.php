<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Contracts\UserServiceInterface;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="User",
 *     description="API untuk mengelola User"
 * )
 */
class UserController extends Controller
{
  protected UserServiceInterface $userService;

  public function __construct(UserServiceInterface $userService)
  {
      $this->userService = $userService;
  }

  /**
   * @OA\Get(
   *     path="/api/user/getuserbygroup/{groupUser}",
   *     summary="Get users by group",
   *     tags={"User"},
   *     operationId="getUserByGroup",
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="groupUser",
   *         in="path",
   *         required=true,
   *         description="The name of the user group",
   *         @OA\Schema(type="string", example="Safety Advisor")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Users retrieved successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="User get data successfully"),
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Users not found or already deleted",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="User get data not found or already deleted"),
   *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
   *         )
   *     )
   * )
   */

  public function getUserByGroup(string $groupUser)
  {
    return $this->userService->getUserByGroup($groupUser);
  }

    /**
   * @OA\Get(
   *     path="/api/user",
   *     summary="Get all users",
   *     tags={"User"},
   *     operationId="getAllUsers",
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="perPage",
   *         in="query",
   *         description="Number of templates per page",
   *         @OA\Schema(type="integer", example=10)
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="List of users",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="User get data successfully"),
   *             @OA\Property(
   *                 property="data",
   *                 type="array",
   *                 @OA\Items(
   *                     @OA\Property(property="UserId", type="string", format="uuid"),
   *                     @OA\Property(property="Username", type="string", example="ekoagung"),
   *                     @OA\Property(property="UserGroup", type="string", example="Safety Advisor"),
   *                     @OA\Property(
   *                         property="user_detail",
   *                         type="object",
   *                         @OA\Property(property="Name", type="string"),
   *                     )
   *                 )
   *             )
   *         )
   *     ),
   * )
   */

  public function getUser(Request $request)
  {
    $user = Auth::user();
    return $this->userService->getUser(
      user: $user,
      perPage: $request->get('perPage', 10),
      sortBy: $request->get('sort_by', 'UserId'),
      sortOrder: $request->get('sort_order', 'desc')
    );
  }

  /**
 * @OA\Get(
 *     path="/api/user/{userId}",
 *     summary="Get user detail by ID",
 *     tags={"User"},
 *     operationId="getUserById",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         description="User ID (UUID)",
 *         @OA\Schema(type="string", format="uuid", example="d547dfc0-5177-4b64-a491-3e41f4c3a312")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User detail retrieved",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User get data successfully"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="UserId", type="string", format="uuid"),
 *                 @OA\Property(property="Name", type="string"),
 *                 @OA\Property(property="Email", type="string"),
 *                 @OA\Property(property="Address", type="string"),
 *                 @OA\Property(property="City", type="string"),
 *                 @OA\Property(property="Phone", type="string")
 *             )
 *         )
 *     ),
 * )
 */
  public function getUserById(string $userId)
  {
    return $this->userService->getUserById($userId);
  }

/**
 * @OA\Put(
 *     path="/api/user/{userId}/change-password",
 *     summary="Change user password",
 *     tags={"User"},
 *     operationId="changePassword",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         description="User ID (UUID)",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"OldPassword", "Password"},
 *             @OA\Property(property="OldPassword", type="string", example="oldpassword123"),
 *             @OA\Property(property="Password", type="string", example="newpassword456")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Password changed successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Password changed successfully")
 *         )
 *     )
 * )
 */

  public function changePassword(Request $request, string $userId)
  {
      $data = $request->only(['OldPassword', 'Password']);
      return $this->userService->changePassword($userId, $data);
  }

  /**
 * @OA\Put(
 *     path="/api/user/{userId}",
 *     summary="Update user details",
 *     tags={"User"},
 *     operationId="updateUser",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         description="User ID (UUID)",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="Name", type="string", example="John Doe"),
 *             @OA\Property(property="Email", type="string", example="john@example.com"),
 *             @OA\Property(property="Address", type="string", example="Jl. Merdeka 123"),
 *             @OA\Property(property="City", type="string", example="Jakarta"),
 *             @OA\Property(property="Phone", type="string", example="081234567890")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User updated successfully")
 *         )
 *     )
 * )
 */

  public function updateUser(Request $request, string $userId)
  {
      $data = $request->only(['Name', 'Email', 'Address', 'City', 'Phone']);
      return $this->userService->updateUser($userId, $data);
  }

  /**
 * @OA\Put(
 *     path="/api/user/{userId}/enabled",
 *     summary="Activate or deactivate specific user by ID (Admin only)",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string", format="uuid"),
 *         description="User ID to activate/deactivate"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"IsActivated"},
 *             @OA\Property(property="IsActivated", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User activation updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="UserId", type="string"),
 *                 @OA\Property(property="Username", type="string"),
 *                 @OA\Property(property="IsActivated", type="boolean")
 *             )
 *         )
 *     ),
 * )
 */
  public function enabledUser(Request $request, string $userId)
  {
    $user = Auth::user();
    return $this->userService->enabledUser(
      $user,
      $userId,
      $request->boolean('IsActivated')
    );
  }

    /**
   * @OA\Delete(
   *     path="/api/user/{userId}",
   *     summary="Delete user by ID (Admin only)",
   *     tags={"User"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="userId",
   *         in="path",
   *         required=true,
   *         description="User ID (UUID)",
   *         @OA\Schema(type="string", format="uuid")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="User deleted successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="data", type="object",
   *                 @OA\Property(property="UserId", type="string"),
   *                 @OA\Property(property="Username", type="string"),
   *                 @OA\Property(property="UserGroup", type="string")
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthorized",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=false),
   *             @OA\Property(property="message", type="string", example="Only Administrator can delete users")
   *         )
   *     ),
   * )
   */
  public function deleteUser(string $userId)
  {
    $user = Auth::user();
    return $this->userService->deleteUser($user, $userId);
  }

  /**
   * @OA\Get(
   *     path="/api/user/groups",
   *     summary="Get all user groups (except Administrator)",
   *     tags={"User"},
   *     @OA\Response(
   *         response=200,
   *         description="List of user groups",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="data", type="array", @OA\Items(
   *                 @OA\Property(property="UserGroup", type="string", example="Supervisor")
   *             ))
   *         )
   *     ),
   * )
   */
  public function getGroup()
  {
    return $this->userService->getGroup();
  }

}
