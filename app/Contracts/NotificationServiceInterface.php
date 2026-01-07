<?php

namespace App\Contracts;

use App\Models\User;

interface NotificationServiceInterface
{
  public function getNotifications(User $user, $paginate = 10 , $limit = 3, string $mode);
  public function markAsRead(User $user, string $notifId);
}
