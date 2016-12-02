<?php

namespace App\Controllers\Sites;

use Libs\Base\Application;
use App\Model\Sites\Site as SiteModel;

class Offline extends SiteBase
{

	public function __construct(Application $app)
	{
		$this->keys = array('site_id');

		$this->methods = array('checkSiteUid');
		parent::__construct($app);
	}

	public function go()
	{
		$SiteModel = new SiteModel;
		$res = $SiteModel->OfflineBySiteId($this->params['site_id']);
		return $res == 1 ? 'success' : 'failure';
	}
}
