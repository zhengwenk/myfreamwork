<?php
namespace App\Controllers\Resource;

use Libs\Base\Controllers;
use Libs\Base\Application;
use App\Model\Resource\Resource as ResModel;

class DelUploadResource extends Controllers
{
	public function __construct(Application $app)
	{
		$this->keys = array('imgid');
		$this->rules = array('imgid' => 'required|integer');
		$this->isReportLogin = true;
		parent::__construct($app);
	}

	public function go()
	{
		$ResModel = new ResModel;
		$ResModel->Del($this->uid, $this->params['imgid']);
		return 'success';
	}
}