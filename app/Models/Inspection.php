<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class Inspection
 *
 * @property string $InspectionId
 * @property string|null $SWAPIC
 * @property string|null $Location
 * @property string|null $WorkType
 * @property array|null $InspectionForm
 * @property bool|null $IsDraft
 * @property string|null $CreatedBy
 * @property Carbon|null $CreatedAt
 *
 * @property UserDetail|null $user_detail
 * @property Collection|WorkInProgress[] $work_in_progress
 * @property Collection|WorkingPermit[] $working_permits
 *
 * @package App\Models
 */
class Inspection extends Model
{
  use HasUuids;
	protected $table = 'Inspection';
	protected $primaryKey = 'InspectionId';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'InspectionForm' => 'json',
		'IsDraft' => 'bool',
    'CreatedAt' => 'datetime'
	];

	protected $fillable = [
    'InspectionId',
		'SWAPIC',
		'Location',
		'WorkType',
		'InspectionForm',
		'IsDraft',
		'CreatedBy',
    'CreatedAt'
	];

	public function user_detail()
	{
		return $this->belongsTo(UserDetail::class, 'CreatedBy');
	}

	public function work_in_progress()
	{
		return $this->hasOne(WorkInProgress::class, 'InspectionId');
	}

	public function working_permits()
	{
		return $this->hasOne(WorkingPermit::class, 'InspectionId');
	}
}
