<?php

namespace App\Http\Controllers\Api;

use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Contracts\NotificationServiceInterface;

/**
 * @OA\Tag(
 *     name="Notification",
 *     description="API untuk mengelola notifikasi"
 * )
 */
class NotificationController extends Controller
{
    private $notificationService;
    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

      /**
     * @OA\Get(
     *     path="/api/notifications",
     *     tags={"Notification"},
     *     summary="Ambil daftar notifikasi untuk user saat ini",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Limit data",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Parameter(
     *         name="mode",
     *         in="query",
     *         required=false,
     *         description="Mode Limit",
     *         @OA\Schema(type="string", example="limit")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Daftar notifikasi"
     *     )
     * )
     */

    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->get('per_page', 10);
        $limit = $request->get('limit', 3);
        $mode = $request->get('mode', 'limit');
        return $this->notificationService->getNotifications($user, $perPage, $limit, $mode);
    }

      /**
     * @OA\Post(
     *     path="/api/notifications/read",
     *     tags={"Notification"},
     *     summary="Tandai notifikasi sebagai dibaca",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"notifId"},
     *             @OA\Property(property="notifId", type="string", example="uuid-notifikasi"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status berhasil"
     *     )
     * )
     */
    public function markAsRead(Request $request)
    {

      $data = $request->validate([
          'notifId' => 'required|string|exists:Notifications,NotifId',
      ]);

      $user = Auth::user();
      return $this->notificationService->markAsRead($user, $data['notifId']);
    }
}
