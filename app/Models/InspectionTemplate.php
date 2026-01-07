<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class InspectionTemplate
 *
 * @property string $TemplateId
 * @property Carbon|null $TemplateDate
 * @property array|null $Content
 * @property string|null $Description
 * @property bool|null $IsActivated
 * @property string|null $CreatedBy
 *
 * @property UserDetail|null $user_detail
 *
 * @package App\Models
 */
class InspectionTemplate extends Model
{

  use HasUuids;
	protected $table = 'InspectionTemplate';
	protected $primaryKey = 'TemplateId';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'TemplateDate' => 'datetime',
		'Content' => 'json',
		'IsActivated' => 'bool'
	];

	protected $fillable = [
    'TemplateId',
		'TemplateDate',
		'Content',
		'Description',
		'IsActivated',
		'CreatedBy'
	];

	public function user_detail()
	{
		return $this->belongsTo(UserDetail::class, 'CreatedBy');
	}
}
