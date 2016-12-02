<?php

namespace App\Controllers\Sites;

use Libs\Base\Application;
use App\Model\Sites\SiteConfig as SiteConfigModel;

class SiteConfig extends SiteBase
{
	public function __construct(Application $app)
	{
		$this->keys = array('site_id', 'site_config');
		$this->rules = array(
			'site_id' => 'required|integer',
			'site_config' => 'required|valid_json_string'
		);
		$this->filter = array('site_config' => 'trim');
		$this->methods = array('checkSiteUid');
		parent::__construct($app);
	}

	public function go()
	{
		$data = $this->removeDataPrefix('site_', $this->params);
		$data['siteid'] = $data['id'];
		$data['uid'] = $this->uid;
		unset($data['id']);
		$sitesModel = new SiteConfigModel;
		$sitesModel->SetSiteConfigBySiteId($data['siteid'], $data);
		return 'success';
	}
}