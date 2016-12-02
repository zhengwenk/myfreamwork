<?php

namespace App\Controllers\Sites;

use Libs\Base\Controllers;
use App\Model\Sites\Site as SitesModel;
use App\Lang\Sites\Site as SitesLang;

class SiteBase extends Controllers
{
	public $isReportLogin = true;

	protected function checkSiteUid()
	{
		if (!$this->params['site_id']) {
			$this->msg = SitesLang::SITEID_EMPTY;
			return;
		}

		$SitesModel = new SitesModel;
		$res = $SitesModel->GetSiteBySiteId($this->params['site_id']);

		if (!key_exists('uid', $res) || $this->uid !== $res['uid']) {
			$this->msg = SitesLang::NO_SITE_ACCESS;
		}
	}

	protected function removeDataPrefix($prefix, $data)
	{
		$res = array();
		array_walk($data, function ($v, $k) use(&$res, $prefix) {
			$key = str_replace($prefix, '', $k);
			$res[$key] = $v;
		});
		return $res;
	}

	public function go()
	{

	}
}