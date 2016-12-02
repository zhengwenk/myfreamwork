<?php

namespace App\Controllers\User;

use Libs\Base\Application;
use Libs\Base\Controllers;
use App\Model\Users\User as UserModel;
use App\Lang\Users\User as UserLang;

class Login extends Controllers
{
	public function __construct(Application $app)
	{

		$this->keys = array('account', 'password');
		$this->rules = array(
			'account' => 'required|valid_email|min_len, 10|max_len, 320',
			'password' => 'required|min_len, 6|max_len,32'
		);

		$this->filter = array(
			'password' => 'trim',
			'account'  => 'trim|sanitize_email'
		);

		parent::__construct($app);
	}

	/**
	 * @return mixed
	 */
	protected function go()
	{
		$userModel = new UserModel();
		$res = $userModel->Login(
			$this->params['account'],
			$this->params['password'],
			$this->request->getClientIp()
		);

		if (is_array($res) && key_exists('uid', $res)) {
			//up
			$token = $userModel->createToken($res['uid'], $this->params['password'], $this->encrypter);
			return array(
				'user' => array(
					'uid' => $res['uid'],
					'account' => $res['email'],
					'ucid' => 1
				),
				'token' => $token
			);
		} else {
			if (key_exists($res, UserLang::$userCode)) {
				$this->code = $res;
				return UserLang::$userCode[$res];
			} else {
				$this->code = UserLang::ERROR_CODE;
				return UserLang::LOGIN_UNKONW_ERROR;
			}
		}
	}
}
