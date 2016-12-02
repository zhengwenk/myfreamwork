<?php
namespace App\Controllers\User;

use Libs\Base\Application;
use Libs\Base\Controllers;
use App\Lang\Users\User as UserLang;
use App\Model\Users\User as UserModel;

class Verify extends Controllers
{
	public function __construct(Application $app)
	{
		$this->keys = array('data');
		$this->rules = array('data' => 'required');
		parent::__construct($app);
	}

	public function go()
	{
		$data = $this->encrypter->decrypt($this->params['data']);
		$data = json_decode($data, true);
		if (is_array($data) && key_exists('time', $data)) {
			if ((time() - $data['time']) < 60*30) {
				$userModel = new UserModel;
				$userModel->UpdateEmailStatus($data['email']);
				$token = $userModel->createToken($data['uid'], $data['pwd'], $this->encrypter);

				return array(
					'user' => array(
						'uid' => $data['uid'],
						'account' => $data['email']
					),
					'token' => $token
				);
			} else {
				$this->code = 400;
				return UserLang::VIERIFY_TIMEOUT;
			}
		} else {
			$this->code = 400;
			return UserLang::VIERIFY_CODE_ERROR;
		}
	}
}
