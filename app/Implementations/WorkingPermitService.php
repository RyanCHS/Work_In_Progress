<?php

namespace App\Implementations;

use App\Models\User;
use App\Models\FileInput;
use App\Models\Inspection;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use App\Models\Notification;
use App\Models\WorkingPermit;
use App\Events\SendAnnouncement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Contracts\WorkingPermitServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class WorkingPermitService implements WorkingPermitServiceInterface
{
  public function requestWorkingPermit(User $user, string $inspectionId, array $data)
  {
    try {
      $result = DB::transaction(function () use ($user, $inspectionId, $data) {
        $workingPermit = WorkingPermit::where('InspectionId', $inspectionId)->firstOrFail();
        $workingPermit->update([
          'WPDate'     => now(),
          'SendTo'     => $data['SendTo'],
        ]);

        Notification::create([
          'NotificationId'  => Str::uuid(),
          'UserId'          => $data['SendTo'],
          'Title'           => 'Request Working Permit',
          'Message'         => 'Inspection has been created',
          'Category'        => 'Inspection',
          'CreatedAt'       => now(),
          'CreatedBy'       => $user->UserId
        ]);

        DB::afterCommit(function () use ($data) {
          SendAnnouncement::dispatch($data['SendTo']);
        });

        return $workingPermit;
      });
      return ApiResponse::success($result);
    } catch (ModelNotFoundException $e) {
      return ApiResponse::notFound('Working Permit not found');
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function uploadWorkingPermit(UploadedFile $file, User $user,array $data, string $wpId)
  {
    try {
      if (!$file->isValid()) {
        throw new \Exception('File tidak valid atau gagal diunggah.');
      }
      $uuid = (string) Str::uuid();
      $extension = $file->getClientOriginalExtension();
      $filename = $uuid . '.' . $extension;
      $path = $file->storeAs('uploads', $filename, 'public');
      if (!Storage::disk('public')->exists('uploads/' . $filename)) {
        throw new \Exception('File gagal disimpan di server.');
      }
      $result = DB::transaction(function () use ($data, $wpId, $uuid, $user) {
        $fileInput = FileInput::create([
          'FileId' => $uuid,
          'WPId' => $wpId,
          'Status' => true,
          'UploadAt' => now(),
          'UploadedBy' => $user->UserId,
        ]);
        $wp = WorkingPermit::where('WPId', $wpId)->firstOrFail();
        $wp->update([
          'Status'          => true,
          'VerificationBy'  => $user->UserId,
        ]);

        Notification::create([
          'NotificationId'  => Str::uuid(),
          'UserId'          => $wp->RequestBy,
          'Title'           => 'Upload Working Permit',
          'Message'         => 'PDF Working Permit has been uploaded',
          'Category'        => 'Working Permit',
          'CreatedAt'       => now(),
          'CreatedBy'       => $user->UserId
        ]);

        DB::afterCommit(function () use ($wp) {
          SendAnnouncement::dispatch($wp->RequestBy);
        });

        return $fileInput;
    });
    return ApiResponse::success($result);

    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function viewWorkingPermit(User $user, $perPage, $sortBy, $sortOrder)
  {
    try {
      $result = null;
      $allowedSorts = [
        'InspectionId',
        'SWAPIC',
        'Location',
        'WorkType',
        'IsDraft',
        'InspectionDate'
      ];
      if (!in_array($sortBy, $allowedSorts)) {
        $sortBy = 'InspectionId';
      }
      $query = Inspection::query();
      if ($user->UserGroup === 'Safety Advisor') {
        $query->select(
          'InspectionId',
          'SWAPIC',
          'Location',
          'WorkType',
          'InspectionForm',
          'IsDraft')
          ->with(['working_permits' => function ($q) use ($user) {
            $q->with(['user_sendto:UserId,Name']);
          }])
          ->where('IsDraft', false)
          ->where('CreatedBy', $user->UserId);
      } elseif ($user->UserGroup === 'Pengawas K3') {
        $query->select(
          'InspectionId',
          'SWAPIC',
          'Location',
          'WorkType',
          'InspectionForm',
          'CreatedBy')
          ->with(['user_detail:UserId,Name'])
          ->with(['working_permits' => function ($q) use ($user) {
              $q->where('SendTo', $user->UserId);
          }])
          ->whereHas('working_permits', function ($q) use ($user) {
              $q->where('SendTo', $user->UserId);
          })
          ->where('IsDraft', false);
      } elseif ($user->UserGroup === 'Administrator') {
        $query->select(
          'InspectionId',
          'SWAPIC',
          'Location',
          'WorkType',
          'InspectionForm',
          'CreatedBy')
          ->with(['user_detail:UserId,Name'])
          ->with(['working_permits' => function ($q) use ($user) {
            $q->with(['user_verification:UserId,Name']);
          }])
          ->where('IsDraft', false);
      } else {
        return ApiResponse::notFound('User group not authorized to view inspections');
      }
       $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
      if ($sortBy === 'InspectionDate') {
        $query->orderByRaw("
        (
            SELECT STR_TO_DATE(
                JSON_UNQUOTE(jt.answer),
                '%Y-%m-%dT%H:%i:%s.%fZ'
            )
            FROM JSON_TABLE(
                InspectionForm,
                '$[*]'
                COLUMNS (
                    field_key VARCHAR(100) PATH '$.key',
                    input_type VARCHAR(50) PATH '$.input_type',
                    answer VARCHAR(50) PATH '$.answer'
                )
            ) AS jt
            WHERE jt.field_key = 'waktu_inspeksi'
              AND jt.input_type = 'date'
            LIMIT 1
          ) {$sortOrder}
        ");
      } else {
        $query->orderBy($sortBy, $sortOrder);
      }
      $result = $query->paginate($perPage);
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function getFileIdByWPId(string $wpId)
  {
    try {
      $result = FileInput::select('FileId')
        ->where('WPId', $wpId)
        ->where('Status', true)
        ->firstOrFail();

      return ApiResponse::success($result);
    } catch (ModelNotFoundException $e) {
      return ApiResponse::notFound('File for Working Permit not found');
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function viewWorkingPermitChart(User $user, string $groupBy = 'today')
  {
    try {

        $query = Inspection::query()
            ->where('Inspection.IsDraft', false)
            ->join('WorkingPermit as wp', 'wp.InspectionId', '=', 'Inspection.InspectionId');

        /*
        |--------------------------------------------------------------------------
        | User Group Filter
        |--------------------------------------------------------------------------
        */
        if ($user->UserGroup === 'Safety Advisor') {
            $query->where('Inspection.CreatedBy', $user->UserId);
        }

        if ($user->UserGroup === 'Pengawas K3') {
            $query->where('wp.SendTo', $user->UserId);
        }

        // Administrator → no filter

        /*
        |--------------------------------------------------------------------------
        | Date Range (Inspection.CreatedAt)
        |--------------------------------------------------------------------------
        */
        switch ($groupBy) {
            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end   = Carbon::now()->endOfWeek();
                break;

            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end   = Carbon::now()->endOfMonth();
                break;

            case 'year':
                $start = Carbon::now()->startOfYear();
                $end   = Carbon::now()->endOfYear();
                break;

            default: // today
                $start = Carbon::today()->startOfDay();
                $end   = Carbon::today()->endOfDay();
                break;
        }

        $query->whereBetween('Inspection.CreatedAt', [$start, $end]);

        /*
        |--------------------------------------------------------------------------
        | Grouping Expression
        |--------------------------------------------------------------------------
        */
        if ($groupBy === 'year') {
            $groupExpr = "DATE_FORMAT(Inspection.CreatedAt, '%Y-%m')";
            $labelExpr = "DATE_FORMAT(Inspection.CreatedAt, '%b')";
        } else {
            $groupExpr = "DATE(Inspection.CreatedAt)";
            $labelExpr = "DATE(Inspection.CreatedAt)";
        }

        /*
        |--------------------------------------------------------------------------
        | Aggregation
        |--------------------------------------------------------------------------
        */
        $data = $query
            ->select(
                DB::raw("$groupExpr as period"),
                DB::raw("$labelExpr as label"),
                DB::raw("SUM(wp.Status = 1) as approved"),
                DB::raw("SUM(wp.Status = 0) as requested")
            )
            ->groupBy('period', 'label')
            ->orderBy('period')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */
        return ApiResponse::success([
            'group_by' => $groupBy,
            'labels' => $data->pluck('label')->values(),
            'series' => [
                [
                    'name' => 'Approved',
                    'data' => $data->pluck('approved')->map(fn ($v) => (int) $v),
                ],
                [
                    'name' => 'Requested',
                    'data' => $data->pluck('requested')->map(fn ($v) => (int) $v),
                ],
            ],
            'summary' => [
                'total' => (int) $data->sum(fn ($i) => $i->approved + $i->rejected),
                'approved' => (int) $data->sum('approved'),
                'requested' => (int) $data->sum('requested'),
            ],
        ]);

    } catch (\Exception $e) {
        return ApiResponse::error($e);
    }
  }

}
