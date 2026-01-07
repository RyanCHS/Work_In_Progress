<?php

namespace App\Contracts;

use App\Models\User;

interface WorkInProgressServiceInterface
{
  public function verificationWorkInProgress(User $user, string $WIPId, array $data, bool $isVerified);
  public function viewWorkInProgress(User $user, $perPage, $sortBy, $sortOrder);
  public function createSWA(User $user, array $data, string $WIPId, bool $isDraft);
  public function updateSWA(User $user, array $data, string $SWAId, bool $isDraft);
  public function deleteSWA(User $user, string $SWAId);
  public function viewSWADetailByInspection(User $user, string $inspectionId);
  public function viewSWA(User $user, $perPage, ?bool $isWIP, string $WIPId);
  public function requestValidationSWA(array $data, string $SWAId);
  public function validationSWA(User $user, bool $isValid, string $SWAId);
  public function viewWorkInProgressChart(User $user,string $groupBy = 'day');
}
