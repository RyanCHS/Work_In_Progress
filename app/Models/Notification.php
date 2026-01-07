<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class Notification
 *
 * @property string $NotifId
 * @property string|null $UserId
 * @property string|null $Title
 * @property string|null $Message
 * @property string|null $Category
 * @property Carbon|null $CreatedAt
 * @property string $CreatedBy
 *
 * @property UserDetail|null $user_detail
 * @property NotificationRead|null $notification_read
 *
 * @package App\Models
 */
class Notification extends Model
{
  use HasUuids;
	protected $table = 'Notifications';
	protected $primaryKey = 'NotifId';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'CreatedAt' => 'datetime'
	];

	protected $fillable = [
    'NotifId',
		'UserId',
		'Title',
		'Message',
		'Category',
		'CreatedAt',
		'CreatedBy'
	];

	public function user_detail()
	{
		return $this->belongsTo(UserDetail::class, 'UserId');
	}

	public function notification_read()
	{
		return $this->hasMany(NotificationRead::class, 'NotifId', 'NotifId');
	}
}
