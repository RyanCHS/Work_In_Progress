<?php


use App\Models\User;

use App\Events\TestWebsocket;
use App\Events\SendAnnouncement;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckEnvironment;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InspectionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\WorkingPermitController;
use App\Http\Controllers\Api\WorkInProgressController;
use Illuminate\Notifications\Events\BroadcastNotificationCreated;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate as JWTAuth;

Route::post('/register', [AuthController::class, 'register']);
Route::get('user/groups', [UserController::class, 'getGroup']);
Route::prefix('auth')->group(function () {
  Route::post('login', [AuthController::class, 'login']);
  Route::post('logout', [AuthController::class, 'logout']);
  Route::middleware('auth:api')->group(function () {
      Route::get('me', [AuthController::class, 'me']);
      Route::post('refresh', [AuthController::class, 'refresh']);
  });
});
Route::prefix('inspection')->group(function () {
  Route::middleware('auth:api')->group(function () {
   Route::get('/template', [InspectionController::class, 'viewInspectionTemplate']);
   Route::get('/template-content/{templateId}', [InspectionController::class, 'inspectionTemplateById']);
   Route::post('/template', [InspectionController::class, 'createInspectionTemplate']);
   Route::put('/template/{templateId}', [InspectionController::class, 'updateInspectionTemplate']);
   Route::delete('/template/{templateId}', [InspectionController::class, 'deleteInspectionTemplate']);
   Route::get('/templates', [InspectionController::class, 'viewInspectionTemplateAll']);
   Route::put('/template/{templateId}/enabled', [InspectionController::class, 'enabledInspectionTemplate']);

   Route::post('/', [InspectionController::class, 'createInspection']);
   Route::put('/{inspectionId}', [InspectionController::class, 'updateInspection']);
   Route::get('/by-user', [InspectionController::class, 'viewInspectionByUser']);
   Route::get('/chart', [InspectionController::class, 'viewInspectionChart']);
   Route::get('/{inspectionId}', [InspectionController::class, 'viewInspectionById']);
   Route::delete('/{inspectionId}', [InspectionController::class, 'deleteInspection']);
  });
});
Route::prefix('user')->group(function () {
  Route::middleware('auth:api')->group(function () {
    Route::get('getuserbygroup/{groupUser}', [UserController::class, 'getUserByGroup']);
    Route::get('/', [UserController::class, 'getUser']);
    Route::get('/{userId}', [UserController::class, 'getUserById']);
    Route::put('/{userId}', [UserController::class, 'updateUser']);
    Route::put('/{userId}/change-password', [UserController::class, 'changePassword']);
    Route::put('/{userId}/enabled', [UserController::class, 'enabledUser']);
    Route::delete('/{userId}', [UserController::class, 'deleteUser']);
  });
});
Route::prefix('working-permit')->group(function () {
  Route::middleware('auth:api')->group(function () {
    Route::put('/request/{inspectionId}', [WorkingPermitController::class, 'requestWorkingPermit']);
    Route::post('/{wpId}/upload', [WorkingPermitController::class, 'uploadWorkingPermit']);
    Route::get('/by-user', [WorkingPermitController::class, 'viewWorkingPermitByUser']);
    Route::get('/chart', [WorkingPermitController::class, 'viewWorkingPermitChart']);
    Route::get('/fileid/{wpId}', [WorkingPermitController::class, 'getFileIdByWPId']);
  });
});
Route::prefix('work-in-progress')->group(function () {
  Route::middleware('auth:api')->group(function () {
    Route::get('/chart', [WorkInProgressController::class, 'viewWorkInProgressChart']);
    Route::get('/by-user', [WorkInProgressController::class, 'viewWorkInProgressByUser']);
    Route::post('/verification/{WIPId}', [WorkInProgressController::class, 'verificationWorkInProgress']);
    Route::post('/swa/{WIPId}', [WorkInProgressController::class, 'createSWA']);
    Route::post('/swa/{WIPId}/update', [WorkInProgressController::class, 'updateSWA']);
    Route::delete('/swa/{WIPId}', [WorkInProgressController::class, 'deleteSWA']);
    Route::get('/swa/{WIPId}', [WorkInProgressController::class, 'viewSWA']);
    Route::get('/swa/detail/{inspectionId}', [WorkInProgressController::class, 'viewSWADetailByInspection']);
    Route::put('/swa/{SWAId}/request-validation', [WorkInProgressController::class, 'requestValidation']);
    Route::put('/swa/{SWAId}/validate', [WorkInProgressController::class, 'validateSWA']);
  });
});
Route::prefix('notifications')->group(function () {
  Route::middleware('auth:api')->group(function () {
    Route::get('/', [NotificationController::class, 'getNotifications']);
    Route::post('/read', [NotificationController::class, 'markAsRead']);
  });
});

Route::post('/test-notify', function () {
  SendAnnouncement::dispatch("All");
  return response()->json(['message' => 'Notification sent successfully']);
  });

