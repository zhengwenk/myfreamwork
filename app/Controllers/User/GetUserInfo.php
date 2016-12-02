<?php
namespace App\Controllers\User;

use Libs\Base\Controllers;
use stdClass;

class GetUserInfo extends Controllers
{
	public function go()
	{
		if (empty($this->userInfo)) {
			$this->code = 400;
			return new stdClass();
		}
		return $this->userInfo;
	}
}
