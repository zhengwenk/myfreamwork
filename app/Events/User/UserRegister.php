<?php

namespace App\Events\User;

use App\Model\Users\User as UserModel;

class UserRegister
{
	public $userModel;

	public function __construct(UserModel $user)
	{
		$this->userModel = $user;
	}
}
