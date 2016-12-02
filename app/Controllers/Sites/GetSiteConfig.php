<?php

namespace App\Controllers\Sites;

use Libs\Base\Application;
use App\Model\Sites\Site as SiteModel;
use App\Model\Sites\SiteConfig as SiteConfigModel;


class GetSiteConfig extends SiteBase
{
	public function __construct(Application $app)
	{
		$this->keys = array('site_id');
		$this->rules = array(
			'site_id' => 'required|integer'
		);
		//$this->methods = array('checkSiteUid');
		parent::__construct($app);
	}

	public function go()
	{
		$SiteConfigModel = new SiteConfigModel;
		$SiteModel = new SiteModel;
		$config = $SiteConfigModel->GetSiteConfigBySiteId($this->params['site_id']);
		$baseInfo = $SiteModel->GetSiteInfoBySiteId($this->params['site_id']);

		return array(
			'site_config' => isset($config['site_config']) ? json_decode($config['site_config'], true) : array(),
			'site_baseInfo' => !empty($baseInfo) ? $baseInfo : array()
		);
	}
}
