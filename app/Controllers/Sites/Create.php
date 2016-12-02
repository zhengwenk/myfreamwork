<?php

namespace App\Controllers\Sites;

use Libs\Base\Controllers;
use Libs\Base\Application;
use App\Lang\Sites\Site as SitesLang;
use App\Model\Sites\Site as SitesModel;

class Create extends Controllers
{

	protected $tids = array(1, 2, 3, 4);
	public function __construct(Application $app)
	{
		$this->keys = array('tplid'); //tid是模板id
		$this->rules = array('tplid' => 'required');
		$this->methods = array('checkTid');
		$this->isReportLogin = true;
		parent::__construct($app);
	}

	public function go()
	{
		$SitesModel = new SitesModel;
		$siteid = $SitesModel->Create($this->uid, $this->userInfo['nickName'], $this->params['tplid']);

		return array('siteid' => $siteid);
	}

	protected function checkTid()
	{
		if (!in_array($this->params['tplid'], $this->tids)) {
			$this->msg = SitesLang::TPLID_INVALID;
		}
	}
}
