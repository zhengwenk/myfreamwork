<?php
namespace App\Controllers\User;

use Libs\Base\Controllers;
use Libs\Base\Application;
use App\Model\Users\User as UserModel;
use App\Lang\Users\User as UserLang;

class ModifyPassword extends Controllers
{
	public function __construct(Application $app)
	{
		$this->keys = array('oldpassword', 'newpassword');
		$this->rules = array(
			'oldpassword' => 'required',
			'newpassword' => 'required'
		);
		parent::__construct($app);
	}

	public function go()
	{
		$userModel = new UserModel;
		if (true === $userModel->checkPassword($this->uid, $this->params['oldpassword'])) {
			if (1 == $userModel->UpdatePassword($this->uid, $this->params['newpassword'])) {
				return 'success';
			} else {
				$this->code = 400;
				return UserLang::MODIFY_PASSWORD_FAILURE;
			}
		} else {
			$this->code = 400;
			return UserLang::$userCode['14001'];
		}
	}
}
