<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class WorkInProgress
 *
 * @property string $WIPId
 * @property string|null $InspectionId
 * @property bool|null $Status
 *
 * @property Inspection|null $inspection
 * @property Collection|StopWorkAuthority[] $stop_work_authorities
 *
 * @package App\Models
 */
class WorkInProgress extends Model
{
  use HasUuids;
	protected $table = 'WorkInProgress';
	protected $primaryKey = 'WIPId';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'Status' => 'bool'
	];

	protected $fillable = [
    'WIPId',
		'InspectionId',
		'Status'
	];

	public function inspection()
	{
		return $this->belongsTo(Inspection::class, 'InspectionId');
	}

	public function stop_work_authorities()
	{
		return $this->hasMany(StopWorkAuthority::class, 'WIPId');
	}
}
