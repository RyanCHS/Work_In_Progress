<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserGroup
 *
 * @property string $Name
 *
 * @property Collection|User[] $users
 *
 * @package App\Models
 */
class UserGroup extends Model
{
	protected $table = 'UserGroup';
	protected $primaryKey = 'Name';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'Name',
	];

	public function users()
	{
		return $this->hasMany(User::class, 'UserGroup');
	}
}
