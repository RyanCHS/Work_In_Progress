<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Contracts\WorkInProgressServiceInterface;

/**
 * @OA\Tag(
 *     name="Work In Progress",
 *     description="Endpoints untuk data Work In Progress"
 * )
 */

class WorkInProgressController extends Controller
{
  protected $WorkInProgressService;

  public function __construct(WorkInProgressServiceInterface $WorkInProgressService)
  {
      $this->WorkInProgressService = $WorkInProgressService;
  }
/**
 * @OA\Get(
 *     path="/api/work-in-progress/by-user",
 *     summary="Get Work In Progress list for authenticated user",
 *     tags={"Work In Progress"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Jumlah item per halaman",
 *         required=false,
 *         @OA\Schema(type="integer", default=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
 *                 @OA\Property(property="total", type="integer", example=25)
 *             )
 *         )
 *     ),
 * )
 */

  public function viewWorkInProgressByUser(Request $request)
  {
    $user = Auth::user();
    return $this->WorkInProgressService->viewWorkInProgress(
      user: $user,
      perPage: $request->get('per_page', 10),
      sortBy: $request->get('sort_by', 'InspectionId'),
      sortOrder: $request->get('sort_order', 'desc')
    );
  }

  /**
 * @OA\Post(
 *     path="/api/work-in-progress/verification/{WIPId}",
 *     summary="Verifikasi dan simpan data Stop Work Authority",
 *     tags={"Work In Progress"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="WIPId",
 *         in="path",
 *         required=true,
 *         description="ID dari Work In Progress",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"InputInspection", "isVerified"},
 *             @OA\Property(property="InputInspection", type="object", example={"field": "value"}),
 *             @OA\Property(property="IsVerified", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Verifikasi berhasil",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 * )
 */
  public function verificationWorkInProgress(Request $request, string $WIPId)
  {
    $user = Auth::user();
    $validated = $request->validate([
      'InputInspection' => 'required|array',
      'IsVerified' => 'required|boolean',
    ]);

    return $this->WorkInProgressService->verificationWorkInProgress($user, $WIPId, $validated, $validated['IsVerified']);
  }

  /**
 * @OA\Post(
 *     path="/api/work-in-progress/swa/{WIPId}",
 *     summary="Create Stop Work Authority (SWA)",
 *     tags={"Work In Progress"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="WIPId",
 *         in="path",
 *         required=true,
 *         description="ID dari Work In Progress",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="InputInspection (JSON string) dan file upload untuk masing-masing field",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"InputInspection"},
 *                 @OA\Property(
 *                     property="InputInspection",
 *                     type="string",
 *                     description="JSON string dari input inspeksi",
 *                     example="[{'key':'sop','answer':'Comply'},{'key':'working_permit','answer':'Comply'}]"
 *                 ),
 *                 @OA\Property(
 *                     property="IsDraft",
 *                     type="boolean",
 *                     description="SWA draft atau tidak",
 *                     example="true"
 *                 ),
 *                 @OA\Property(
 *                     property="FileInput[0][key]",
 *                     type="string",
 *                     description="Key tag untuk file",
 *                     example="sop"
 *                 ),
 *                 @OA\Property(
 *                     property="FileInput[0][fileinput]",
 *                     type="string",
 *                     format="binary",
 *                     description="File PDF untuk key"
 *                 ),
 *                 @OA\Property(
 *                     property="FileInput[1][key]",
 *                     type="string",
 *                     example="working_permit"
 *                 ),
 *                 @OA\Property(
 *                     property="FileInput[1][fileinput]",
 *                     type="string",
 *                     format="binary"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="SWA berhasil dibuat",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 * )
 */
  public function createSWA(Request $request, string $WIPId)
  {
    $user = Auth::user();

    $request->validate([
      'InputInspection' => 'required|string',
      'IsDraft' => 'required|boolean',
      'FileInput' => 'sometimes|array',
      'FileInput.*.key' => 'required|string',
      'FileInput.*.fileinput' => 'required|file|mimes:pdf|max:3072',
    ]);

    $data = [
      'InputInspection' => $request->InputInspection, // string
      'FileInput' => $request->FileInput ?? [],
    ];

    return $this->WorkInProgressService->createSWA($user, $data, $WIPId, $request->IsDraft);
  }
  /**
 * @OA\Post(
 *     path="/api/work-in-progress/swa/{SWAId}/update",
 *     summary="Update Stop Work Authority (SWA)",
 *     tags={"Work In Progress"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="SWAId",
 *         in="path",
 *         required=true,
 *         description="ID dari SWA yang ingin diupdate",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Form input inspeksi dan file jika ada perubahan",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"InputInspection", "IsDraft"},
 *                 @OA\Property(
 *                     property="InputInspection",
 *                     type="string",
 *                     description="JSON string dari input inspeksi",
 *                     example="[{'key':'sop','answer':'Comply'}]"
 *                 ),
 *                 @OA\Property(
 *                     property="IsDraft",
 *                     type="boolean",
 *                     description="Tandai apakah masih draft atau final",
 *                     example=true
 *                 ),
 *                 @OA\Property(
 *                     property="FileInput[0][key]",
 *                     type="string",
 *                     example="sop"
 *                 ),
 *                 @OA\Property(
 *                     property="FileInput[0][fileinput]",
 *                     type="string",
 *                     format="binary"
 *                 ),
 *                 @OA\Property(
 *                     property="FileInput[1][key]",
 *                     type="string",
 *                     example="working_permit"
 *                 ),
 *                 @OA\Property(
 *                     property="FileInput[1][fileinput]",
 *                     type="string",
 *                     format="binary"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="SWA berhasil diperbarui",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 * )
 */
  public function updateSWA(Request $request, string $SWAId)
  {
    $user = Auth::user();

    $validated = $request->validate([
      'InputInspection' => 'required|string',
      'IsDraft' => 'required|boolean',
      'FileInput' => 'sometimes|array',
      'FileInput.*.key' => 'required|string',
      'FileInput.*.fileinput' => 'required|file|mimes:pdf|max:3072',
    ]);

    return $this->WorkInProgressService->updateSWA(
      $user,
      $validated,
      $SWAId,
      filter_var($validated['IsDraft'], FILTER_VALIDATE_BOOLEAN)
    );
  }
  /**
   * @OA\Delete(
   *     path="/api/work-in-progress/swa/{SWAId}",
   *     summary="Menghapus Stop Work Authority (SWA)",
   *     tags={"Work In Progress"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="SWAId",
   *         in="path",
   *         required=true,
   *         description="ID dari SWA yang akan dihapus",
   *         @OA\Schema(type="string", format="uuid")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="SWA berhasil dihapus",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true)
   *         )
   *     ),
   * )
   */
  public function deleteSWA(string $SWAId)
  {
      $user = Auth::user();
      return $this->WorkInProgressService->deleteSWA($user, $SWAId);
  }
/**
 * @OA\Get(
 *     path="/api/work-in-progress/swa/{WIPId}",
 *     summary="Lihat daftar SWA berdasarkan WIP",
 *     tags={"Work In Progress"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="WIPId",
 *         in="path",
 *         required=true,
 *         description="ID dari Work In Progress",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\Parameter(
 *         name="IsWIP",
 *         in="query",
 *         required=false,
 *         description="Filter IsWIP (true/false)",
 *         @OA\Schema(type="boolean")
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         required=false,
 *         description="Jumlah data per halaman",
 *         @OA\Schema(type="integer", default=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data SWA berhasil diambil",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 * )
 */
  public function viewSWA(Request $request, string $WIPId)
  {
    $user = Auth::user();
    $isWIP = $request->query('IsWIP');
    $isWIP = is_null($isWIP) ? null : filter_var($isWIP, FILTER_VALIDATE_BOOLEAN);
    $perPage = $request->query('per_page');
    $perPage = is_null($perPage) ? null : $perPage;

    return $this->WorkInProgressService->viewSWA($user, $perPage, $isWIP, $WIPId);
  }

  /**
 * @OA\Get(
 *     path="/api/work-in-progress/swa/detail/{inspectionId}",
 *     summary="Lihat data SWA berdasarkan Inspection ID",
 *     tags={"Work In Progress"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="inspectionId",
 *         in="path",
 *         required=true,
 *         description="ID dari Inspection",
 *         @OA\Schema(type="string", format="uuid")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Data SWA ditemukan",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 * )
 */

  public function viewSWADetailByInspection(string $inspectionId)
  {
    $user = Auth::user();
    return $this->WorkInProgressService->viewSWADetailByInspection($user, $inspectionId);
  }

  /**
   * @OA\Put(
   *     path="/api/work-in-progress/swa/{SWAId}/request-validation",
   *     summary="Meminta validasi untuk SWA oleh user tertentu",
   *     tags={"Work In Progress"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="SWAId",
   *         in="path",
   *         required=true,
   *         description="ID dari Stop Work Authority (SWA)",
   *         @OA\Schema(type="string", format="uuid")
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"ValidatedBy"},
   *             @OA\Property(property="ValidatedBy", type="string", format="uuid", example="user-uuid")
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Validator berhasil diset",
   *         @OA\JsonContent(@OA\Property(property="success", type="boolean", example=true))
   *     ),
   * )
   */
    public function requestValidation(Request $request, string $SWAId)
    {
      $data = $request->validate([
        'ValidatedBy' => 'required|uuid|exists:UserDetail,UserId',
      ]);

      return $this->WorkInProgressService->requestValidationSWA($data, $SWAId);
    }
  /**
   * @OA\Put(
   *     path="/api/work-in-progress/swa/{SWAId}/validate",
   *     summary="Melakukan validasi SWA oleh validator yang ditugaskan",
   *     tags={"Work In Progress"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="SWAId",
   *         in="path",
   *         required=true,
   *         description="ID dari Stop Work Authority (SWA)",
   *         @OA\Schema(type="string", format="uuid")
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"isValidation"},
   *             @OA\Property(property="isValidation", type="boolean",  example=true)
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="SWA berhasil divalidasi",
   *         @OA\JsonContent(@OA\Property(property="success", type="boolean", example=true))
   *     ),
   * )
   */
  public function validateSWA(Request $request, string $SWAId)
  {
    $user = Auth::user();
    $validated = $request->validate([
      'isValidation' => 'required|boolean',
    ]);

    return $this->WorkInProgressService->validationSWA($user,$validated['isValidation'], $SWAId);
  }

  /**
 * @OA\Get(
 *     path="/api/work-in-progress/chart",
 *     summary="Get Work In Progress chart",
 *     description="Chart of Work In Progress status grouped by date and filtered by user role",
 *     tags={"Work In Progress"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="group_by",
 *         in="query",
 *         required=false,
 *         description="Grouping period",
 *         @OA\Schema(
 *             type="string",
 *             enum={"today","day","week","month","year"},
 *             example="month"
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Chart data retrieved",
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
  public function viewWorkInProgressChart(Request $request)
  {
    $user = Auth::user();
    return $this->WorkInProgressService->viewWorkInProgressChart(
      user: $user,
      groupBy: $request->get('group_by', 'day')
    );
  }
}
