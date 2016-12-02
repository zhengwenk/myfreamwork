<?php

namespace App\Controllers\Sites;

use Libs\Base\Controllers;
use Libs\Base\Application;
use App\Model\Sites\Site as SiteModel;


class GetSiteId extends Controllers
{
	public function __construct(Application $app)
	{
		$this->keys = array('domain');
		$this->rules = array('domain' => 'required');
		parent::__construct($app);
	}

	public function go()
	{
		$domain = str_replace('site-', '', $this->params['domain']);
		$SiteModel = new SiteModel;

		$siteId = $SiteModel->GetSiteIdByDomain($domain);

		if ($siteId == 0) {
			$this->code = 400;
			return 'failure';
		} else {
			return array('site_id' => $siteId);
		}
	}
}
