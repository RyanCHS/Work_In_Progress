<?php

namespace App\Contracts;

use App\Models\User;


interface InspectionServiceInterface
{
  public function viewInspectionTemplate(User $user, ?bool $isDraft);
  public function inspectionTemplateById(string $templateId);
  public function createInspectionTemplate(User $user, array $data);
  public function updateInspectionTemplate(User $user, string $templateId, array $data);
  public function deleteInspectionTemplate(User $user, string $templateId);
  public function viewInspectionTemplateAll(User $user, $perPage = 10);
  public function enabledInspectionTemplate(User $user, bool $isActivated , string $templateId);
  public function createInspection(User $user,array $data);
  public function updateInspection(User $user, string $inspectionId, array $data);
  public function viewInspection(User $user, $perPage, $sortBy, $sortOrder, ?bool $isDraft);
  public function viewInspectionById(string $inspectionId);
  public function deleteInspection(User $user,string $inspectionId);
  public function viewInspectionChart(User $user, string $groupBy = 'day');
}
