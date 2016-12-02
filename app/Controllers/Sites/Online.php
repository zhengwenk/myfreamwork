<?php

namespace App\Controllers\Sites;

use Libs\Base\Application;
use App\Model\Sites\Site as SitesModel;
use App\Events\Sites\SiteCreate;

class Online extends SiteBase
{

	public function __construct(Application $app)
	{
		$this->keys = array('site_id', 'site_name', 'site_domain', 'site_typeid');
		$this->rules = array(
			'site_id' => 'required|integer',
			'site_name' => 'max_len, 100|min_len, 0',
			'site_domain' => 'max_len, 32|min_len, 0|alpha_dash'
		);

		$this->filter = array(
			'site_name' => 'trim|sanitize_string',
			'site_typeid' => 'trim|sanitize_numbers',
			'site_domain' => 'trim|sanitize_string'
		);
		$this->methods = array('checkSiteUid');
		parent::__construct($app);
	}

	public function go()
	{
		$data = $this->removeDataPrefix('site_', $this->params);
		$siteid = $data['id'];
		unset($data['id']);

		$sitesModel = new SitesModel;

		$siteInfo = $sitesModel->GetSiteInfoBySiteId($siteid);

		if (isset($siteInfo['online_num']) && $siteInfo['online_num'] == 0) {
			$this->app['events']->fire(new SiteCreate($siteid));
		}

		if ($sitesModel->OnlineBySiteId($siteid, $data)) {
			return 'success';
		} else {
			return 'failure';
		}
	}
}
