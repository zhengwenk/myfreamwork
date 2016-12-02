<?php

namespace App\Controllers\Sites;

use Libs\Base\Application;
use App\Model\Sites\Site as SitesModel;

class GetSiteInfo extends SiteBase
{
	public function __construct(Application $app)
	{
		$this->keys = array('site_id');
		$this->rules = array(
			'site_id' => 'required|integer',
		);
		$this->methods = array('checkSiteUid');
		parent::__construct($app);
	}

	public function go()
	{
		$data = $this->removeDataPrefix('site_', $this->params);
		$sitesModel = new SitesModel;
		$res = $sitesModel->GetSiteInfoBySiteId($data['id']);
		return key_exists('name', $res) ? $res : array();
	}
}