<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StopWorkAuthority
 *
 * @property string $SWAId
 * @property string|null $WIPId
 * @property Carbon|null $SWADate
 * @property array|null $InputInspection
 * @property bool|null $IsDraft
 * @property bool|null $Status
 * @property bool|null $IsWIP
 * @property bool|null $RepairSWA
 * @property bool|null $DoRepairSWA
 * @property string|null $ValidatedBy
 * @property Carbon|null $ValidatedAt
 * @property string|null $SendTo
 * @property string|null $CreatedBy
 *
 * @property WorkInProgress|null $work_in_progress
 * @property UserDetail|null $user_detail
 * @property Collection|FileInput[] $file_inputs
 *
 * @package App\Models
 */
class StopWorkAuthority extends Model
{
	protected $table = 'StopWorkAuthority';
	protected $primaryKey = 'SWAId';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'SWADate' => 'datetime',
		'InputInspection' => 'json',
    'IsDraft' => 'bool',
		'Status' => 'bool',
    'IsWIP' => 'bool',
    'RepairSWA' => 'bool',
    'DoRepairSWA' => 'bool',
    'ValidatedAt' => 'datetime',
	];

	protected $fillable = [
		'SWAId',
		'WIPId',
		'SWADate',
		'InputInspection',
    'IsDraft',
		'Status',
    'IsWIP',
    'RepairSWA',
    'DoRepairSWA',
		'ValidatedBy',
    'ValidatedAt',
    'SendTo',
		'CreatedBy'
	];

	public function work_in_progress()
	{
		return $this->belongsTo(WorkInProgress::class, 'WIPId');
	}

	public function created_by()
	{
		return $this->belongsTo(UserDetail::class, 'CreatedBy');
	}

  public function validated_by()
	{
		return $this->belongsTo(UserDetail::class, 'ValidatedBy');
	}

  public function send_to()
	{
		return $this->belongsTo(UserDetail::class, 'SendTo');
	}

	public function file_inputs()
	{
		return $this->hasMany(FileInput::class, 'SWAId', 'SWAId');
	}

}
