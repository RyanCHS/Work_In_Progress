<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Contracts\InspectionServiceInterface;

/**
 * @OA\Tag(
 *     name="Inspection",
 *     description="API untuk mengelola Inspection"
 * )
 */
class InspectionController extends Controller
{
  private $inspectionService;

  public function __construct(InspectionServiceInterface $inspectionService)
  {
    $this->inspectionService = $inspectionService;
  }

    /**
   * @OA\Get(
   *     path="/api/inspection/template",
   *     summary="Get inspection template data by ID and optional isDraft",
   *     tags={"Inspection"},
   *     operationId="viewInspectionTemplate",
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="IsDraft",
   *         in="query",
   *         required=false,
   *         description="Filter by active isDraft (true/false)",
   *         @OA\Schema(type="boolean")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Success",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Template data retrieved successfully"),

  *         )
  *     ),
  *     @OA\Response(response=404, description="Template not found")
  * )
  */
  public function viewInspectionTemplate(Request $request)
  {
    $user = Auth::user();
    $isDraft = $request->query('IsDraft');
    $isDraft = is_null($isDraft) ? null : filter_var($isDraft, FILTER_VALIDATE_BOOLEAN);
    return $this->inspectionService->viewInspectionTemplate($user, $isDraft);
  }

  /**
   * @OA\Get(
   *     path="/api/inspection/template-content/{templateId}",
   *     summary="Get full template content by ID",
   *     tags={"Inspection"},
   *     operationId="inspectionTemplateById",
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="templateId",
   *         in="path",
   *         required=true,
   *         description="Template ID",
   *         @OA\Schema(type="string", format="uuid")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Success",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Template content retrieved successfully"),

  *         )
  *     ),
  *     @OA\Response(response=404, description="Template not found")
  * )
  */
  public function inspectionTemplateById(string $templateId)
  {
    return $this->inspectionService->inspectionTemplateById($templateId);
  }

  /**
   * @OA\Post(
   *     path="/api/inspection/template",
   *     summary="Create a new inspection template",
   *     tags={"Inspection"},
   *     security={{"bearerAuth":{}}},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"Content", "Description", "IsActivated", "CreatedBy"},
   *             @OA\Property(property="Content", type="object", example={"field": "value"}),
   *             @OA\Property(property="Description", type="string", example="Template Deskripsi"),
   *             @OA\Property(property="IsActivated", type="boolean", example=true),
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Success",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Template created successfully"),

  *         )
  *     )
  * )
  */
  public function createInspectionTemplate(Request $request)
  {
    $user = Auth::user();
    $validated = $request->validate([
      'Content' => 'required|array',
      'Description' => 'required|string',
      'IsActivated' => 'required|boolean',
    ]);

    return $this->inspectionService->createInspectionTemplate($user, $validated);
  }

  /**
   * @OA\Put(
   *     path="/api/inspection/template/{templateId}",
   *     summary="Update an existing inspection template",
   *     tags={"Inspection"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="templateId",
   *         in="path",
   *         required=true,
   *         description="Template ID",
   *         @OA\Schema(type="string", format="uuid")
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             @OA\Property(property="Content", type="object", example={"field": "value"}),
   *             @OA\Property(property="Description", type="string", example="Template Deskripsi"),
   *             @OA\Property(property="IsActivated", type="boolean", example=true),
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Update success",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Template updated successfully"),

  *         )
  *     )
  * )
  */
  public function updateInspectionTemplate(Request $request, string $templateId)
  {
    $user = Auth::user();
    $validated = $request->only(['Content', 'Description', 'IsActivated']);
    return $this->inspectionService->updateInspectionTemplate($user, $templateId, $validated);
  }

  /**
   * @OA\Delete(
   *     path="/api/inspection/template/{templateId}",
   *     summary="Delete an inspection template",
   *     tags={"Inspection"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="templateId",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string", format="uuid"),
   *         description="Template ID"
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Success",
   *         @OA\JsonContent(
   *             @OA\Property(property="success", type="boolean", example=true),
   *             @OA\Property(property="message", type="string", example="Template deleted successfully")
   *         )
   *     )
   * )
   */
  public function deleteInspectionTemplate(string $templateId)
  {
    $user = Auth::user();
    return $this->inspectionService->deleteInspectionTemplate($user, $templateId);
  }
   /**
     * @OA\Get(
     *     path="/api/inspection/templates",
     *     summary="Get all inspection templates created by current user",
     *     tags={"Inspection"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="perPage",
     *         in="query",
     *         description="Number of templates per page",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of inspection templates",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function viewInspectionTemplateAll(Request $request)
    {
      $user = Auth::user();
      return $this->inspectionService->viewInspectionTemplateAll($user, $request->get('perPage', 10));
    }

     /**
     * @OA\Put(
     *     path="/api/inspection/template/{templateId}/enabled",
     *     summary="Enable or disable an inspection template",
     *     tags={"Inspection"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="templateId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid"),
     *         description="Template ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"IsActivated"},
     *             @OA\Property(property="IsActivated", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Template activation updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function enabledInspectionTemplate(Request $request, string $templateId)
    {
      $user = Auth::user();
      $isActivated = $request->boolean('IsActivated');
      return $this->inspectionService->enabledInspectionTemplate($user, $isActivated, $templateId);
    }
  /**
   * @OA\Post(
   *     path="/api/inspection",
   *     summary="Create a new inspection record",
   *     tags={"Inspection"},
   *     security={{"bearerAuth":{}}},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"SWAPIC", "Location", "WorkType", "InspectionDate", "WorkingPermit", "InspectionForm", "isDraft", "CreatedBy"},
   *             @OA\Property(property="SWAPIC", type="string", example="SW-001"),
   *             @OA\Property(property="Location", type="string", example="Site A"),
   *             @OA\Property(property="WorkType", type="string", example="Electrical"),
   *             @OA\Property(property="InspectionDate", type="string", format="date-time", example="2024-05-10T10:00:00Z"),
   *             @OA\Property(property="InspectionForm", type="object", example={"field": "value"}),
   *             @OA\Property(property="IsDraft", type="boolean", example=true),
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Inspection created successfully"
   *     )
   * )
   */
  public function createInspection(Request $request)
  {
    $user = Auth::user();
    $data = $request->validate([
        'SWAPIC' => 'required|string',
        'Location' => 'required|string',
        'WorkType' => 'required|string',
        'InspectionForm' => 'required|array',
        'IsDraft' => 'required|boolean',
    ]);

    return $this->inspectionService->createInspection($user, $data);
  }

  /**
   * @OA\Put(
   *     path="/api/inspection/{inspectionId}",
   *     summary="Update an existing inspection",
   *     tags={"Inspection"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="inspectionId",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string", format="uuid")
   *     ),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(
   *             required={"SWAPIC", "Location", "WorkType", "InspectionDate","WorkingPermit", "InspectionForm", "isDraft"},
   *             @OA\Property(property="SWAPIC", type="string", example="SW-001"),
   *             @OA\Property(property="Location", type="string", example="Site A"),
   *             @OA\Property(property="WorkType", type="string", example="Electrical"),
   *             @OA\Property(property="InspectionForm", type="object", example={"field": "value"}),
   *             @OA\Property(property="IsDraft", type="boolean", example=true),
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Inspection updated successfully"
   *     )
   * )
   */
  public function updateInspection(Request $request, string $inspectionId)
  {
    $user = Auth::user();
    $data = $request->validate([
      'SWAPIC' => 'required|string',
      'Location' => 'required|string',
      'WorkType' => 'required|string',
      'InspectionForm' => 'required|array',
      'IsDraft' => 'required|boolean',
    ]);

    return $this->inspectionService->updateInspection($user, $inspectionId, $data);
  }

  /**
   * @OA\Get(
   *     path="/api/inspection/by-user",
   *     summary="View inspection list based on authenticated user's role",
   *     tags={"Inspection"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="per_page",
   *         in="query",
   *         required=false,
   *         @OA\Schema(type="integer", example=10)
   *     ),
   *     @OA\Parameter(
   *         name="IsDraft",
   *         in="query",
   *         required=false,
   *         description="Filter by active isDraft (true/false)",
   *         @OA\Schema(type="boolean")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Inspections retrieved"
   *     )
   * )
   */
  public function viewInspectionByUser(Request $request)
  {
    $user = Auth::user();

    $isDraft = $request->query('IsDraft');
    $isDraft = is_null($isDraft) ? null : filter_var($isDraft, FILTER_VALIDATE_BOOLEAN);

    return $this->inspectionService->viewInspection(
      user: $user,
      perPage: $request->get('per_page', 10),
      sortBy: $request->get('sort_by', 'InspectionId'),
      sortOrder: $request->get('sort_order', 'desc'),
      isDraft: $isDraft
    );
  }

 /**
 * @OA\Get(
 *
 *     path="/api/inspection/chart",
 *     summary="Get inspection chart grouped by time period",
 *     description="Return inspection count grouped by day, week, month, or year for authenticated user",
 *     tags={"Inspection"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="group_by",
 *         in="query",
 *         required=false,
 *         description="Group chart data by time period",
 *         @OA\Schema(
 *             type="string",
 *             enum={"day","week","month","year"},
 *             example="day"
 *         )
 *     ),
 *
 *
 *     @OA\Response(
 *         response=200,
 *         description="Inspections retrieved"
 *     )
 * )
 */
  public function viewInspectionChart(Request $request)
  {
    $user = Auth::user();
    return $this->inspectionService->viewInspectionChart(
      user: $user,
      groupBy: $request->get('group_by', 'day')
    );
  }
  /**
   * @OA\Get(
   *     path="/api/inspection/{inspectionId}",
   *     summary="Get inspection details by ID",
   *     tags={"Inspection"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="inspectionId",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string", format="uuid")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Inspection details"
   *     )
   * )
   */
  public function viewInspectionById(string $inspectionId)
  {
    return $this->inspectionService->viewInspectionById($inspectionId);
  }

  /**
   * @OA\Delete(
   *     path="/api/inspection/{inspectionId}",
   *     summary="Delete an inspection by ID",
   *     tags={"Inspection"},
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(
   *         name="inspectionId",
   *         in="path",
   *         required=true,
   *         @OA\Schema(type="string", format="uuid")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Inspection deleted"
   *     )
   * )
   */
  public function deleteInspection(string $inspectionId)
  {
    $user = Auth::user();
    return $this->inspectionService->deleteInspection($user, $inspectionId);
  }

}
