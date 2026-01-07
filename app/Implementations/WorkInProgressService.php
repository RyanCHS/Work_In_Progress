<?php

namespace App\Implementations;

use App\Models\User;
use App\Models\FileInput;
use App\Models\Inspection;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use App\Models\WorkingPermit;
use App\Models\WorkInProgress;
use App\Models\StopWorkAuthority;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Contracts\WorkInProgressServiceInterface;

class WorkInProgressService implements WorkInProgressServiceInterface
{
  public function viewWorkInProgress(User $user, $perPage, $sortBy, $sortOrder)
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
          'InspectionForm')
          ->where('CreatedBy', $user->UserId)
          ->with([
            'working_permits' => function ($wp) {
              $wp->select('WPId','InspectionId', 'SendTo')
              ->where('Status', true);
            },
            'work_in_progress' => function ($wip)  {
              $wip->select('WIPId', 'InspectionId', 'Status')
                ->withCount(['stop_work_authorities'])
                ->with(['stop_work_authorities' => function ($swa) {
                  $swa->where(function ($q)  {
                    $q->where('IsDraft', false)
                      ->orWhere('RepairSWA', true);
                  });
                }]);
              }
            ])
            ->whereHas('working_permits', function ($wp) {
              $wp->where('Status', true);
            })
            ->whereHas('work_in_progress')
            ->where('IsDraft', false);
      } elseif ($user->UserGroup === 'Pengawas K3') {
        $query->select(
          'InspectionId',
          'SWAPIC',
          'Location',
          'WorkType',
          'InspectionForm',
          'CreatedBy')
          ->whereHas('working_permits', function ($wp) use ($user) {
              $wp->where('Status', true)
                ->where('SendTo', $user->UserId);
          })
          ->whereHas('work_in_progress', function ($wip) {
              $wip->whereHas('stop_work_authorities' , function ($swa) {
                  $swa->where(function ($q) {
                    $q->where('IsDraft', false)
                      ->where('RepairSWA', true)
                      ->orWhere('Status', true);
                  });
              });
          })
          ->with([
              'user_detail:UserId,Name',
              'working_permits' => function ($wp) use ($user) {
                  $wp->select('WPId', 'InspectionId', 'SendTo')
                    ->where('Status', true)
                    ->where('SendTo', $user->UserId);
              },
              'work_in_progress' => function ($wip)  {
                  $wip->select('WIPId', 'InspectionId', 'Status')
                      ->withCount(['stop_work_authorities'])
                      ->with(['stop_work_authorities' => function ($swa) {
                        $swa->where(function ($q)  {
                          $q->where('IsDraft', false)
                            ->orWhere('RepairSWA', true);
                        });
                      }]);
              }
            ]);
      } elseif ($user->UserGroup === 'Administrator') {
        $query->select(
          'InspectionId',
          'SWAPIC',
          'Location',
          'WorkType',
          'InspectionForm',
          'CreatedBy')
          ->whereHas('working_permits', function ($wp) use ($user) {
              $wp->where('Status', true);
          })
          ->whereHas('work_in_progress', function ($wip) {
              $wip->whereHas('stop_work_authorities' , function ($swa) {
                  $swa->where(function ($q) {
                    $q->where('IsDraft', false)
                      ->where('RepairSWA', true)
                      ->orWhere('Status', true);
                  });
              });
          })
          ->with([
              'user_detail:UserId,Name',
              'working_permits' => function ($wp) use ($user) {
                  $wp->select('WPId', 'InspectionId', 'SendTo','VerificationBy')
                    ->where('Status', true)
                    ->with(['user_verification:UserId,Name']);
              },
              'work_in_progress' => function ($wip)  {
                  $wip->select('WIPId', 'InspectionId', 'Status')
                      ->withCount(['stop_work_authorities'])
                      ->with(['stop_work_authorities' => function ($swa) {
                        $swa->where(function ($q)  {
                          $q->where('IsDraft', false)
                            ->orWhere('RepairSWA', true);
                        });
                      }]);
              }
            ]);
      } else {
        return ApiResponse::notFound('User group not authorized to view inspections');
      }
      $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
      $query->orderBy($sortBy, $sortOrder);
      $result = $query->paginate($perPage);
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function verificationWorkInProgress(User $user, string $WIPId, array $data, bool $isVerified)
  {
    try {
      $result = DB::transaction(function () use ($user, $WIPId, $data, $isVerified) {
        $swa = StopWorkAuthority::create ([
          'SWAId'           => Str::uuid(),
          'WIPId'           => $WIPId,
          'SWADate'         => now(),
          'InputInspection' => $data['InputInspection'],
          'Status'          => $isVerified,
          'IsWIP'           => true,
          'CreatedBy'       => $user->UserId
        ]);
        if ($isVerified) {
          $swa->update(['VerificationBy' => $user->UserId]);
          WorkInProgress::where('WIPId', $WIPId)->update(['Status' => true]);
        }
        return $swa;
      });
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function viewSWADetailByInspection(User $user, string $inspectionId)
  {
    try {
      $result = null;
      if ($user->UserGroup === 'Safety Advisor' || $user->UserGroup === 'Administrator') {
        $inspection = Inspection::select('InspectionId', 'InspectionForm')
        ->with([
          'working_permits' => fn($wp) => $wp->select('WPId', 'InspectionId')->where('Status', true),
          'work_in_progress' => fn($wip) => $wip->select('WIPId', 'InspectionId', 'Status')
        ])
        ->where('InspectionId', $inspectionId)
        ->orderByDesc('CreatedAt')
        ->first();

        $stopWorkAuthorities = StopWorkAuthority::where('WIPId', $inspection->work_in_progress->WIPId)
          ->with(['file_inputs' => fn($fi) =>
            $fi->select('FileId','SWAId','Keytag','Status','UploadAt','UploadedBy')])
          ->select('SWAId','WIPId','SWADate','InputInspection','IsDraft','Status','IsWIP','ValidatedBy','CreatedBy');

        $result = [
          'inspection' => $inspection,
          'stop_work_authorities' => $stopWorkAuthorities
        ];
      } else {
        return ApiResponse::notFound('User group not authorized to view inspections');
      }
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function createSWA(User $user, array $data, string $WIPId, bool $isDraft)
  {
    try {
      $result = null;
      if ($user->UserGroup === 'Safety Advisor') {
        $result = DB::transaction(function () use ($user, $data, $WIPId, $isDraft) {
          $inputInspection = json_decode($data['InputInspection'], true);
          $InspectionId = WorkInProgress::where('WIPId', $WIPId)->value('InspectionId');
          $SendTo = WorkingPermit::where('InspectionId', $InspectionId)->value('SendTo');
          if (json_last_error() !== JSON_ERROR_NONE) {
              throw new \Exception('InputInspection harus berupa JSON yang valid.');
          }
          $swa = StopWorkAuthority::create([
              'SWAId'           => Str::uuid(),
              'WIPId'           => $WIPId,
              'SWADate'         => now(),
              'InputInspection' => $inputInspection,
              'IsDraft'         => $isDraft,
              'RepairSWA'       => !$isDraft ? true : false,
              'SendTo'          => !$isDraft ? $SendTo : null,
              'CreatedBy'       => $user->UserId
          ]);

          $excludeKeys = ['nama_pemegang', 'lokasi_pekerjaan', 'jenis_pekerjaan', 'waktu_inspeksi'];

            foreach ($data['FileInput'] ?? [] as $fileData) {
                $key = $fileData['key'] ?? null;
                $file = $fileData['fileinput'] ?? null;

                if (
                    $key &&
                    !in_array($key, $excludeKeys) &&
                    $file instanceof UploadedFile
                ) {
                    $fileId = Str::uuid();

                    FileInput::create([
                        'FileId'     => $fileId,
                        'SWAId'      => $swa->SWAId,
                        'Keytag'     => $key,
                        'Status'     => true,
                        'UploadAt'   => now(),
                        'UploadedBy' => $user->UserId
                    ]);
                    $filename = "{$fileId}.{$file->getClientOriginalExtension()}";
                    $file->storeAs('uploads', "{$filename}", 'public');
                    if (!Storage::disk('public')->exists('uploads/' . $filename)) {
                      throw new \Exception('File gagal disimpan di server.');
                    }
                }
            }
            return $swa;
        });

      } elseif ($user->UserGroup === 'Pengawas K3') {
        $result = DB::transaction(function () use ($user, $data, $WIPId) {
          $inputInspection = json_decode($data['InputInspection'], true);
          $InspectionId = WorkInProgress::where('WIPId', $WIPId)->value('InspectionId');
          $SendTo = WorkingPermit::where('InspectionId', $InspectionId)->value('RequestBy');
          if (json_last_error() !== JSON_ERROR_NONE) {
              throw new \Exception('InputInspection harus berupa JSON yang valid.');
          }
          $swa = StopWorkAuthority::create([
              'SWAId'           => Str::uuid(),
              'WIPId'           => $WIPId,
              'SWADate'         => now(),
              'InputInspection' => $inputInspection,
              'IsDraft'         => false,
              'DoRepairSWA'     => true,
              'SendTo'          => $SendTo,
              'CreatedBy'       => $user->UserId
          ]);

          $excludeKeys = ['nama_pemegang', 'lokasi_pekerjaan', 'jenis_pekerjaan', 'waktu_inspeksi'];

            foreach ($data['FileInput'] ?? [] as $fileData) {
                $key = $fileData['key'] ?? null;
                $file = $fileData['fileinput'] ?? null;

                if (
                    $key &&
                    !in_array($key, $excludeKeys) &&
                    $file instanceof UploadedFile
                ) {
                    $fileId = Str::uuid();

                    FileInput::create([
                        'FileId'     => $fileId,
                        'SWAId'      => $swa->SWAId,
                        'Keytag'     => $key,
                        'Status'     => true,
                        'UploadAt'   => now(),
                        'UploadedBy' => $user->UserId
                    ]);
                    $filename = "{$fileId}.{$file->getClientOriginalExtension()}";
                    $file->storeAs('uploads', "{$filename}", 'public');
                    if (!Storage::disk('public')->exists('uploads/' . $filename)) {
                      throw new \Exception('File gagal disimpan di server.');
                    }
                }
            }
            return $swa;
        });
      } else {
        return ApiResponse::notFound('User group not authorized to create SWA');
      }
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function updateSWA(User $user, array $data, string $SWAId, bool $isDraft)
  {
    try {
      if ($user->UserGroup === 'Safety Advisor') {
        $result = DB::transaction(function () use ($user, $data, $SWAId, $isDraft) {
          $inputInspection = json_decode($data['InputInspection'], true);
          $WIPId = StopWorkAuthority::where('SWAId', $SWAId)->value('WIPId');
          $InspectionId = WorkInProgress::where('WIPId', $WIPId)->value('InspectionId');
          $SendTo = WorkingPermit::where('InspectionId', $InspectionId)->value('SendTo');
          if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('InputInspection harus berupa JSON yang valid.');
          }

          $swa = StopWorkAuthority::where('SWAId', $SWAId)->firstOrFail();

          if ($swa->CreatedBy !== $user->UserId) {
              throw new \Exception('Anda tidak berhak mengedit SWA ini.');
          }

          $swa->InputInspection = $inputInspection;
          $swa->IsDraft = $isDraft;
          if (!$isDraft) {
            $swa->RepairSWA = true;
            $swa->SendTo = $SendTo;
          }
          $swa->save();

          $excludeKeys = ['nama_pemegang', 'lokasi_pekerjaan', 'jenis_pekerjaan', 'waktu_inspeksi'];

          foreach ($data['FileInput'] ?? [] as $fileData) {
            $key = $fileData['key'] ?? null;
            $file = $fileData['fileinput'] ?? null;

            if (
                $key &&
                !in_array($key, $excludeKeys) &&
                $file instanceof UploadedFile
            ) {
                $existing = FileInput::where('SWAId', $swa->SWAId)
                  ->where('Keytag', $key)
                  ->first();

                $fileId = $existing ? $existing->FileId : Str::uuid();
                $filename = "{$fileId}.{$file->getClientOriginalExtension()}";
                $path = "uploads/{$filename}";

                $shouldSave = true;

                if (Storage::disk('public')->exists($path)) {
                  $oldContent = Storage::disk('public')->get($path);
                  $newContent = file_get_contents($file->getRealPath());

                  if (md5($oldContent) === md5($newContent)) {
                    $shouldSave = false; // file tidak berubah
                  }
                }

                if ($shouldSave) {
                  $file->storeAs('uploads', $filename, 'public');

                  if (!Storage::disk('public')->exists($path)) {
                      throw new \Exception("File gagal disimpan di server.");
                  }

                  if ($existing) {
                    $existing->UploadAt = now();
                    $existing->UploadedBy = $user->UserId;
                    $existing->Status = true;
                    $existing->save();
                  } else {
                    FileInput::create([
                      'FileId'     => $fileId,
                      'SWAId'      => $swa->SWAId,
                      'Keytag'     => $key,
                      'Status'     => true,
                      'UploadAt'   => now(),
                      'UploadedBy' => $user->UserId,
                    ]);
                  }
                }
              }
            }

            return $swa;
          });
          return ApiResponse::success($result);
      }
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function deleteSWA(User $user, string $SWAId)
  {
    try {
      $result = StopWorkAuthority::where('SWAId', $SWAId)->firstOrFail();

      if ($result->CreatedBy !== $user->UserId) {
          throw new \Exception("Anda tidak memiliki hak untuk menghapus SWA ini.");
      }
      $result->delete();

      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function viewSWA(User $user, $perPage, ?bool $isWIP, string $WIPId)
  {
    try {
      $result = null;
      if ($user->UserGroup === 'Safety Advisor' || $user->UserGroup === 'Administrator') {
        $querySafetyAdvisor = StopWorkAuthority::select('SWAId','WIPId','SWADate','InputInspection','IsDraft','Status','IsWIP')
        ->where('WIPId', $WIPId)
        ->where('RepairSWA', true)
        ->with(['file_inputs:FileId,SWAId,Keytag,Status,UploadAt,UploadedBy']);

        if (!is_null($isWIP)) {
            $querySafetyAdvisor->where('IsWIP', $isWIP);
        }

        $safetyAdvisor = $querySafetyAdvisor->get(); // Ambil satu data (detail view)

        $queryRepairSWA = StopWorkAuthority::select('SWAId','WIPId','SWADate','InputInspection','Status', 'DoRepairSWA', 'IsWIP', 'CreatedBy', 'SendTo', 'ValidatedBy','ValidatedAt')
            ->where('IsWIP', false)
            ->where('WIPId', $WIPId)
            ->where('DoRepairSWA', true)
            ->with([
                'created_by:UserId,Name',
                'send_to:UserId,Name',
                'validated_by:UserId,Name',
                'file_inputs:FileId,SWAId,Keytag,Status,UploadAt,UploadedBy'
            ]);

        $repairSWA = $queryRepairSWA->paginate($perPage); // Ambil daftar repair SWA

        $result = [
            'safetyAdvisor' => $safetyAdvisor,
            'repairSWA' => $repairSWA,

        ];

        return ApiResponse::success($result);
      }
      else {
        return ApiResponse::notFound('User group not authorized to view inspections');
      }
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function requestValidationSWA(array $data, string $SWAId)
  {
    try {
        $result = StopWorkAuthority::where('SWAId', $SWAId)->update([
          'ValidatedBy' => $data['ValidatedBy'],
        ]);
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function validationSWA(User $user, bool $isValid, string $SWAId)
  {
    try {
      $result = DB::transaction(function () use ($user,$isValid,$SWAId) {
        $swa = StopWorkAuthority::where('SWAId', $SWAId)->firstOrFail();

        $swa->ValidatedBy = $user->UserId;
        $swa->ValidatedAt = now();
        $swa->Status = $isValid;
        $swa->save();

        if ($isValid) {
          WorkInProgress::where('WIPId', $swa->WIPId)->update(['Status' => true]);
        }

        return $swa;
      });
      return ApiResponse::success($result);
    } catch (\Exception $e) {
      return ApiResponse::error($e);
    }
  }

  public function viewWorkInProgressChart(User $user, string $groupBy = 'week')
  {
    try {
        $query = Inspection::query()
            ->leftJoin('WorkInProgress as wip', 'wip.InspectionId', '=', 'Inspection.InspectionId')
            ->leftJoin('StopWorkAuthority as swa', 'swa.WIPId', '=', 'wip.WIPId')
            ->where('Inspection.IsDraft', false);

        /*
        |--------------------------------------------------------------------------
        | User Group Filter
        |--------------------------------------------------------------------------
        */
        if ($user->UserGroup === 'Safety Advisor') {
            $query->where('Inspection.CreatedBy', $user->UserId);
        }

        if ($user->UserGroup === 'Pengawas K3') {
            $query->leftJoin('WorkingPermit as wp', 'wp.InspectionId', '=', 'Inspection.InspectionId')
                  ->where('wp.SendTo', $user->UserId);
        }
        // Administrator → no filter

        /*
        |--------------------------------------------------------------------------
        | Date Range Auto Filter
        |--------------------------------------------------------------------------
        */
        switch ($groupBy) {
            case 'day':
                $query->whereDate('Inspection.CreatedAt', Carbon::today());
                break;

            case 'week':
                $query->whereBetween('Inspection.CreatedAt', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
                break;

            case 'month':
                $query->whereMonth('Inspection.CreatedAt', Carbon::now()->month)
                      ->whereYear('Inspection.CreatedAt', Carbon::now()->year);
                break;

            case 'year':
                $query->whereYear('Inspection.CreatedAt', Carbon::now()->year);
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Grouping Expression
        |--------------------------------------------------------------------------
        */
        switch ($groupBy) {
            case 'week':
                $period = "DATE(Inspection.CreatedAt)";
                $label  = "DATE_FORMAT(Inspection.CreatedAt, '%Y-%m-%d')";
                break;

            case 'month':
                $period = "DATE_FORMAT(Inspection.CreatedAt, '%Y-%m')";
                $label  = "DATE_FORMAT(Inspection.CreatedAt, '%b')";
                break;

            case 'year':
                $period = "YEAR(Inspection.CreatedAt)";
                $label  = "YEAR(Inspection.CreatedAt)";
                break;

            default: // day
                $period = "DATE(Inspection.CreatedAt)";
                $label  = "DATE_FORMAT(Inspection.CreatedAt, '%Y-%m-%d')";
                break;
        }

        /*
        |--------------------------------------------------------------------------
        | Aggregation
        |--------------------------------------------------------------------------
        */
        $data = $query->select(
                DB::raw("$period as period"),
                DB::raw("$label as label"),

                // GO
                DB::raw("COUNT(DISTINCT CASE WHEN wip.Status = 1 THEN wip.WIPId END) as go"),

                // STOP
                DB::raw("COUNT(DISTINCT CASE WHEN swa.Status = 0 THEN swa.SWAId END) as stop")
            )
            ->groupBy('period', 'label')
            ->orderBy('period')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */
        $summary = [
            'total' => $data->sum(fn ($d) => (int)$d->go + (int)$d->stop),
            'go'    => $data->sum(fn ($d) => (int)$d->go),
            'stop'  => $data->sum(fn ($d) => (int)$d->stop),
        ];

        return ApiResponse::success([
            'group_by' => $groupBy,
            'labels'   => $data->pluck('label'),
            'series'   => [
                [
                    'name' => 'GO',
                    'data' => $data->pluck('go')->map(fn ($v) => (int) $v),
                ],
                [
                    'name' => 'STOP',
                    'data' => $data->pluck('stop')->map(fn ($v) => (int) $v),
                ],
            ],
            'summary' => $summary,
        ]);

    } catch (\Exception $e) {
        return ApiResponse::error($e);
    }
  }





}
