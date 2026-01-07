<?php

namespace App\Models;

use App\Models\Notification;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 *
 * @property string $UserId
 * @property string $Username
 * @property string|null $Password
 * @property string|null $UserGroup
 * @property bool|null $IsActivated
 *
 * @property UserGroup|null $user_group
 * @property UserDetail $user_detail
 *
 * @package App\Models
 */
class User extends Authenticatable implements JWTSubject
{

  use Notifiable;

	protected $table = 'User';
	protected $primaryKey = 'Username';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'IsActivated' => 'bool'
	];

	protected $fillable = [
		'UserId',
    'Username',
		'Password',
		'UserGroup',
		'IsActivated'
	];

	public function user_group()
	{
		return $this->belongsTo(UserGroup::class, 'UserGroup');
	}

	public function user_detail()
  {
    return $this->hasOne(UserDetail::class, 'UserId', 'UserId');
  }

  public function getJWTIdentifier()
  {
      return $this->getKey();
  }

  public function getJWTCustomClaims(): array
  {
      return [];
  }

  public function getAuthPassword()
  {
      return $this->Password; // ini akan digunakan oleh JWTAuth
  }


}
