<?php
namespace App\Controllers\User;

use Libs\Base\Controllers;
use Libs\Base\Application;
use App\Model\Users\User as UserModel;

class UpdateUserInfo extends Controllers
{
	public function __construct(Application $app)
	{
		$this->keys = array('nickName');
		$this->rules = array('nickName' => 'required|max_len,30');
		$this->filter = array('nickName' => 'trim');
		parent::__construct($app);
	}

	public function go()
	{
		$userModel = new UserModel;
		$userModel->UpdateUserInfo($this->uid, $this->params);
		return 'success';
	}
}
