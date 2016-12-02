<?php
namespace App\Controllers\Resource;

use Libs\Base\Controllers;
use Libs\Base\Application;
use App\Model\Resource\Resource as ResModel;
use App\Lang\Resource\Resource as ResLang;

class UploadImageList extends Controllers
{
	public function __construct(Application $app)
	{
		$this->keys = array('limit');
		$this->isReportLogin = true;
		parent::__construct($app);
	}

	public function go()
	{
		$ResModel = new ResModel;
		$res = $ResModel->GetImageListByUid($this->uid, $this->params['limit']);
		return is_array($res) ? $res : array();
	}
}