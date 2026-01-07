<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class WorkingPermit
 *
 * @property string $WPId
 * @property string|null $InspectionId
 * @property Carbon|null $WPDate
 * @property bool|null $Status
 * @property string|null $SendTo
 * @property string|null $VerificationBy
 * @property string|null $RequestBy
 *
 * @property UserDetail|null $user_detail
 * @property UserDetail|null $user_verification
 * @property Inspection|null $inspection
 * @property Collection|FileInput[] $file_inputs
 *
 * @package App\Models
 */
class WorkingPermit extends Model
{
  use HasUuids;
	protected $table = 'WorkingPermit';
	protected $primaryKey = 'WPId';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'WPDate' => 'datetime',
		'Status' => 'bool'
	];

	protected $fillable = [
		'InspectionId',
		'WPDate',
		'Status',
		'SendTo',
		'VerificationBy',
		'RequestBy'
	];

	public function user_detail()
	{
		return $this->belongsTo(UserDetail::class, 'RequestBy');
	}

	public function user_sendto()
	{
		return $this->belongsTo(UserDetail::class, 'SendTo');
	}

  public function user_verification()
	{
		return $this->belongsTo(UserDetail::class, 'VerificationBy');
	}

	public function inspection()
	{
		return $this->belongsTo(Inspection::class, 'InspectionId');
	}

  public function file_inputs()
  {
      return $this->hasMany(FileInput::class, 'WPId', 'WPId');
  }
}
