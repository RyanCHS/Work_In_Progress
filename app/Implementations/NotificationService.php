<?php
namespace App\Implementations;

use App\Models\User;
use App\Helpers\ApiResponse;
use App\Models\Notification;
use App\Contracts\NotificationServiceInterface;

class NotificationService implements NotificationServiceInterface
{
  public function getNotifications(User $user, $paginate = 10 , $limit = 3, string $mode)
  {
    try {
      $query = Notification::with(['notification_read' => function ($q) use ($user) {
          $q->where('UserId', $user->UserId);
        }])
        ->where(function ($q) use ($user) {
          $q->whereNull('UserId')
            ->orWhere('UserId', $user->UserId);
        })
        ->orderByDesc('CreatedAt');

      $result = $mode === 'limit'
        ? $query->limit($limit)->get()
        : $query->paginate($paginate);

      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function markAsRead(User $user, string $notifId)
  {
    try {
      $result = NotificationRead::firstOrCreate(
        [
          'NotifId' => $notifId,
          'UserId' => $user->UserId,
        ],
        [
          'ReadAt' => now(),
        ]
    );
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }
}
