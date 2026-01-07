<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class NotificationRead
 * 
 * @property string $NotifId
 * @property string|null $UserId
 * @property Carbon|null $ReadAt
 * 
 * @property Notification $notification
 *
 * @package App\Models
 */
class NotificationRead extends Model
{
	protected $table = 'NotificationReads';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ReadAt' => 'datetime'
	];

	protected $fillable = [
		'NotifId',
		'UserId',
		'ReadAt'
	];

	public function notification()
	{
		return $this->belongsTo(Notification::class, 'NotifId');
	}
}
