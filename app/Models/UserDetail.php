<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class UserDetail
 *
 * @property string $UserId
 * @property string|null $Name
 * @property string|null $Email
 * @property string|null $Address
 * @property string|null $City
 * @property string|null $Phone
 *
 * @property Collection|FileInput[] $file_inputs
 * @property Collection|Inspection[] $inspections
 * @property Collection|InspectionTemplate[] $inspection_templates
 * @property Collection|StopWorkAuthority[] $stop_work_authorities
 * @property Collection|User[] $users
 * @property Collection|WorkingPermit[] $working_permits
 *
 * @package App\Models
 */
class UserDetail extends Model
{
  use HasUuids;
  protected $table = 'UserDetail';
	protected $primaryKey = 'UserId';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
    'UserId',
		'Name',
		'Email',
		'Address',
		'City',
		'Phone'
	];

	public function file_inputs()
	{
		return $this->hasMany(FileInput::class, 'UploadedBy');
	}

	public function inspections()
	{
		return $this->hasMany(Inspection::class, 'CreatedBy');
	}

	public function inspection_templates()
	{
		return $this->hasMany(InspectionTemplate::class, 'CreatedBy');
	}

	public function swa_created_by()
	{
		return $this->hasMany(StopWorkAuthority::class, 'CreatedBy');
	}

  public function swa_verification_by()
	{
		return $this->hasMany(StopWorkAuthority::class, 'VerificationBy');
	}

	public function users()
	{
    return $this->belongsTo(User::class, 'UserId', 'UserId');
	}

	public function working_permits()
	{
		return $this->hasMany(WorkingPermit::class, 'RequestBy');
	}

}
