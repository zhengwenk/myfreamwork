<?php

namespace App\Controllers\User;

use Libs\Base\Controllers;
use Libs\Base\Application;
use App\Lang\Users\User as UserLang;
use App\Model\Users\User;
use App\Events\User\UserRegister;

class Register extends Controllers
{
	public function __construct(Application $app)
	{
		$this->keys = array('account', 'password', 'nickName');
		$this->rules = array(
			'account' => 'required|valid_email|min_len, 10|max_len, 320',
			'password' => 'required|min_len, 6|max_len,32',
			'nickName' => 'required|max_len,30'
		);

		$this->filter = array(
			'password' => 'trim',
			'account'  => 'trim|sanitize_email',
			'nickName' => 'trim'
		);
		parent::__construct($app);
	}

	public function go()
	{
		$userModel = new User;
		$result = $userModel->Register(
			$this->params['account'],
			$this->params['password'],
			$this->params['nickName'],
			$this->request->getClientIp()
		);
		if (true === $result ) {
			$this->app['events']->fire(new UserRegister($userModel));
			return 1;
		} else {
			if (array_key_exists($result, UserLang::$userCode)) {
				$this->code = $result;
				return UserLang::$userCode[$result];
			} else {
				$this->code = 400;
				return UserLang::REG_UNKONW_ERROR;
			}
		}
	}
}
