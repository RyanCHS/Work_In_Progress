<?php

namespace App\Contracts;
use App\Models\User;
use Illuminate\Http\Request;

interface UserServiceInterface
{
  public function createUser(array $data);
  public function login(Request $request);
  public function logout();
  public function me();
  public function getUserByGroup(string $groupUser);
  public function getUser(User $user, $perPage, $sortBy, $sortOrder);
  public function getGroup();
  public function getUserById(string $userId);
  public function updateUser(string $userId, array $data);
  public function changePassword(string $userId, array $data);
  public function enabledUser(User $user,string $userId, bool $isActivated);
  public function deleteUser(User $user,string $userId);
}
