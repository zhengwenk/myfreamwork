<?php

namespace App\Controllers;

use App\Model\Sites\Site as SiteModel;
use App\Lang\Sites\Site as SitesLang;
use Libs\Base\Controllers;

class Base extends Controllers
{
	public function CheckSiteUid()
	{
		$SiteModel = new SiteModel;
		$siteId = isset($this->params['site_id']) ? $this->params['site_id'] : $this->params['app_id'];
		if (! $SiteModel->CheckSiteMaster($siteId, $this->uid)) {
			$this->code = 400;
			$this->msg = SitesLang::NO_SITE_ACCESS;
		}
	}

	public function go(){}
}
