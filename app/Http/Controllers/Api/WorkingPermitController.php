<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Implementations\WorkingPermitService;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Working Permit",
 *     description="API untuk mengelola Working Permit"
 * )
 */
class WorkingPermitController extends Controller
{
  protected $workingPermitService;

  public function __construct(WorkingPermitService $workingPermitService)
  {
      $this->workingPermitService = $workingPermitService;
  }

  /**
   * Request Working Permit by Inspection ID
   *
   * @OA\Put(
   *     path="/api/working-permit/request/{inspectionId}",
   *     tags={"Working Permit"},
   *     summary="Request Working Permit",
   *     description="Membuat atau memperbarui working permit berdasarkan InspectionId",
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="inspectionId",
   *         in="path",
   *         required=true,
   *         description="ID dari Inspection",
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"SendTo"},
   *             @OA\Property(property="SendTo", type="string",  format="uuid", example="user_id_123")
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Working permit berhasil diproses",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="data", type="object")
   *         )
   *     ),
   * )
   */
  public function requestWorkingPermit(Request $request, string $inspectionId)
  {
    $user = Auth::user();
    $validated = $request->validate([
      'SendTo' => 'required|string',
    ]);
    return $this->workingPermitService->requestWorkingPermit($user, $inspectionId, $validated);
  }

  /**
   * @OA\Get(
   *     path="/api/working-permit/by-user",
   *     summary="Menampilkan daftar Inspection beserta Working Permit-nya",
   *     tags={"Working Permit"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="perPage",
   *         in="query",
   *         required=false,
   *         description="Jumlah item per halaman",
   *         @OA\Schema(type="integer", example=10)
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Daftar inspection beserta working permit berhasil diambil",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="data", type="object")
   *         )
   *     ),
   * )
   */
  public function viewWorkingPermitByUser(Request $request)
  {
    $user = Auth::user();
    $perPage = $request->query('perPage', 10);
    return $this->workingPermitService->viewWorkingPermit(
      user: $user,
      perPage: $request->get('per_page', 10),
      sortBy: $request->get('sort_by', 'InspectionId'),
      sortOrder: $request->get('sort_order', 'desc'));
    }

  /**
 * Upload file ke Working Permit
 *
 * @OA\Post(
 *     path="/api/working-permit/{wpId}/upload",
 *     summary="Upload file PDF ke Working Permit",
 *     tags={"Working Permit"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="wpId",
 *         in="path",
 *         required=true,
 *         description="ID Working Permit",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"file", "UploadedBy"},
 *                 @OA\Property(
 *                     property="file",
 *                     type="string",
 *                     format="binary",
 *                     description="File PDF maksimal 3MB"
 *                 ),
 *                 @OA\Property(
 *                     property="UploadedBy",
 *                     type="string",
 *                     description="User ID dari pengunggah (untuk verifikasi)"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Berhasil upload file",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="file_id", type="string"),
 *             @OA\Property(property="filename", type="string"),
 *             @OA\Property(property="path", type="string")
 *         )
 *     ),
 * )
 */
  public function uploadWorkingPermit(Request $request, string $wpId)
  {
    $request->validate([
      'file' => 'required|file|mimes:pdf|max:3072',
      'UploadedBy' => 'required|string|exists:UserDetail,UserId',
    ]);

    $user = Auth::user();
    $file = $request->file('file');
    $data = $request->only('UploadedBy');

    return $this->workingPermitService->uploadWorkingPermit($file, $user, $data, $wpId);
  }

  /**
   * @OA\Get(
   *     path="/api/working-permit/fileid/{wpId}",
   *     summary="Get FileId by Working Permit ID",
   *     tags={"Working Permit"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="wpId",
   *         in="path",
   *         required=true,
   *         description="Working Permit ID",
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Success",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="data", type="object",
   *                 @OA\Property(property="FileId", type="integer", example=123)
   *             )
   *         )
   *     ),
   * )
   */
  public function getFileIdByWPId(string $wpId)
  {
    return $this->workingPermitService->getFileIdByWPId($wpId);
  }

  /**
 * @OA\Get(
 *     path="/api/working-permit/chart",
 *     summary="Working Permit chart statistics",
 *     tags={"Working Permit"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="group_by",
 *         in="query",
 *         required=false,
 *         description="Group by time",
 *         @OA\Schema(
 *             type="string",
 *             enum={"day","week","month","year"},
 *             example="month"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Chart data",
 *     )
 * )
 */
  public function viewWorkingPermitChart(Request $request)
  {
    $user = Auth::user();
    return $this->workingPermitService->viewWorkingPermitChart(
        user: $user,
        groupBy: $request->get('group_by', 'day')
    );
  }


}
