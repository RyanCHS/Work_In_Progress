<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


/**
 * Class FileInput
 *
 * @property string $FileId
 * @property string|null $WPId
 * @property string|null $SWAId
 * @property string|null $Keytag
 * @property bool|null $Status
 * @property Carbon|null $UploadAt
 * @property string|null $UploadedBy
 *
 * @property WorkingPermit|null $working_permit
 * @property UserDetail|null $user_detail
 *
 * @package App\Models
 */
class FileInput extends Model
{
  use HasUuids;
	protected $table = 'FileInput';
	protected $primaryKey = 'FileId';
	public $incrementing = false;
  protected $keyType = 'string';
	public $timestamps = false;

	protected $casts = [
		'Status' => 'bool',
    'UploadAt' => 'datetime'
	];

	protected $fillable = [
    'FileId',
    'WPId',
    'SWAId',
    'Keytag',
		'Status',
    'UploadAt',
		'UploadedBy'
	];

	public function working_permit()
	{
		return $this->belongsTo(WorkingPermit::class, 'WPId', 'WPId');
	}

	public function stop_work_authority()
	{
		return $this->belongsTo(StopWorkAuthority::class, 'SWAId', 'SWAId');
	}

	public function user_detail()
	{
		return $this->belongsTo(UserDetail::class, 'UploadedBy');
	}
}
