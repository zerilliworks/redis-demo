<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use RedisDemo\ModelTraits\AttributeInterface;
use RedisDemo\ModelTraits\AttributeTrait;

class User extends Eloquent implements UserInterface, RemindableInterface, AttributeInterface {

	use UserTrait, RemindableTrait, AttributeTrait, SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */

    protected $fillable = ['name', 'password'];
	protected $table = 'test_users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token', 'created_at', 'updated_at', 'deleted_at');

    public function statuses()
    {
        return $this->hasMany('Status');
    }

    public function comments()
    {
        return $this->hasMany('Comment');
    }

    public function friends()
    {
        return $this->belongsToMany('User', 'test_friends', 'user_id', 'friend_id');
    }

}
