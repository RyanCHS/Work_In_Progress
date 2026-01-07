<?php

namespace App\Implementations;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserDetail;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Contracts\UserServiceInterface;
use App\Exceptions\InvalidCredentialsException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;


class UserService implements UserServiceInterface
{

  public function createUser(array $data)
  {
    try {
      $uuid = (string) Str::uuid();

      $result = DB::transaction(function () use ($data, $uuid) {
        UserDetail::create([
            'UserId' => $uuid,
            'Name'   => $data['Name']
        ]);

        return User::create([
            'UserId'      => $uuid,
            'Username'    => $data['Username'],
            'Password'    => Hash::make($data['Password']),
            'UserGroup'   => $data['UserGroup'],
            'IsActivated' => $data['IsActivated'],
        ])->only(['UserId', 'Username']);
      });

      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }

  }

  public function login(Request $request)
  {
    try {
      $credentials = [
        'Username' => $request->input('Username'),
        'password' => $request->input('Password'),
      ];

      $user = User::where('Username', $credentials['Username'])->first();

      if (!$user || !$user->IsActivated) {
        throw new Exception("User belum diaktifkan atau tidak ditemukan", 401);
      }

      if (!$token = JWTAuth::attempt($credentials)) {
        throw new InvalidCredentialsException();
      }
      return response()->json([
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => JWTAuth::factory()->getTTL() * 120,
      ]);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function logout()
  {
    try {
      if (JWTAuth::getToken()) {
        JWTAuth::parseToken()->invalidate(true);
      }
      return ApiResponse::success('Successfully logged out');
    } catch (\PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException $e) {
      return ApiResponse::error($e);
    }
  }

  public function me()
  {
    try {
      $user = JWTAuth::parseToken()->authenticate();
      $user->load('user_detail'); // pastikan relasi dimuat

      return response()->json([
        'UserId' => $user->UserId,
        'Username' => $user->Username,
        'Name' => $user->user_detail?->Name,
        'UserGroup' => $user->UserGroup,
      ]);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function getUserByGroup(string $groupUser)
  {
    try {
      $result = User::select('UserId', 'Username', 'UserGroup')
      ->with('user_detail:UserId,Name')
      ->where('UserGroup', $groupUser)
      ->where('IsActivated', true)
      ->get();
      if($result->isEmpty()) {
        return ApiResponse::notFound('User not found');
      }
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function getUser(User $user, $perPage, $sortBy, $sortOrder)
  {
    try {
      $result = null;
      $allowedSorts = [
          'UserId',
          'Username',
          'UserGroup',
          'IsActivated'
      ];
      $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
      if (!in_array($sortBy, $allowedSorts)) {
          $sortBy = 'UserId';
      }
      if ($user->UserGroup === 'Administrator') {
        $result = User::select(
          'UserId',
          'Username',
          'UserGroup',
          'IsActivated')
        ->where('UserGroup', '!=', 'Administrator')
        ->with('user_detail:UserId,Name')
        ->orderBy($sortBy, $sortOrder)
        ->paginate($perPage);
      }
      if($result->isEmpty()) {
        return ApiResponse::notFound('User not found');
      }
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function getUserById(string $userId)
  {
    try {
      $result = User::select('UserId', 'Username', 'UserGroup')
      ->with('user_detail:UserId,Name,Email,Address,City,Phone')
      ->where('UserId', $userId)
      ->where('IsActivated', true)
      ->first();
      if(is_null($result)) {
        return ApiResponse::notFound('User not found');
      }
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function updateUser(string $userId, array $data)
  {
    try {
      $result = DB::transaction(function () use ($userId, $data) {
        $userDetail = UserDetail::findOrFail($userId);
        $userDetail->update([
          'Name' => $data['Name'] ?? $userDetail->Name,
          'Email' => $data['Email'] ?? $userDetail->Email,
          'Address' => $data['Address'] ?? $userDetail->Address,
          'City' => $data['City'] ?? $userDetail->City,
          'Phone' => $data['Phone'] ?? $userDetail->Phone,
        ]);
        return $userDetail;
      });
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }
  public function changePassword(string $userId, array $data)
  {
    try {
      $result = DB::transaction(function () use ($userId, $data) {
        $user = User::where('UserId', $userId)->firstOrFail();
        if (!Hash::check($data['OldPassword'], $user->Password)) {
          throw new InvalidCredentialsException();
        }
        $user->update([
          'Password' => Hash::make($data['Password']),
        ]);
        return "Password updated successfully";
      });
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function enabledUser(User $user, string $userId, bool $isActivated)
  {
    try {
      if ($user->UserGroup !== 'Administrator') {
        return ApiResponse::unauthorized('Only Administrator can perform this action');
      }

      $userTemp = User::where('UserId', $userId)->firstOrFail();

      $result = DB::transaction(function () use ($isActivated, $userTemp) {
          $userTemp->update([
              'IsActivated' => $isActivated,
          ]);
          return $userTemp;
      });

      return ApiResponse::success($result);

    } catch (ModelNotFoundException $e) {
        return ApiResponse::notFound('User not found');
    } catch (\Exception $e) {
        return ApiResponse::error($e);
    }
  }

  public function deleteUser(User $user, string $userId)
  {
    try {
      if ($user->UserGroup !== 'Administrator') {
        return ApiResponse::unauthorized('Only Administrator can delete users');
      }

      $userTemp = User::where('UserId', $userId)->firstOrFail();

      $result = DB::transaction(function () use ($userTemp) {
          $userTemp->delete();
          return $userTemp;
      });

      return ApiResponse::success($result);

    } catch (ModelNotFoundException $e) {
        return ApiResponse::notFound('User not found');
    } catch (\Exception $e) {
        return ApiResponse::error($e);
    }
  }

  public function getGroup()
  {
    try {
      $result = UserGroup::select('Name')
        ->where('Name', '!=', 'Administrator')
        ->get();
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

}
