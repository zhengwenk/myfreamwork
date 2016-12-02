<?php

namespace App\Controllers\Sites;

use Libs\Base\Application;
use App\Model\Sites\Site as SitesModel;

class BaseInfo extends SiteBase
{
	public function __construct(Application $app)
	{
		$this->keys = array('site_id', 'site_name', 'site_typeid', 'site_desc', 'site_keyword');
		$this->methods = array('checkSiteUid');
		$this->isReportLogin = true;
		$this->rules = array(
			'site_id' => 'required|integer',
			'site_name' => 'required|max_len, 100',
			'site_typeid' => 'required|max_numeric, 100',
			'site_desc' => 'max_len, 100',
			'site_keyword' => 'max_len, 30'
		);
		$this->filter = array(
			'site_name' => 'trim|sanitize_string',
			'site_typeid' => 'trim|sanitize_numbers',
			'site_desc' => 'trim|sanitize_string',
			'site_keyword' => 'trim|sanitize_string'
		);
		parent::__construct($app);
	}

	public function go()
	{
		$data = $this->removeDataPrefix('site_', $this->params);
		$siteid = $data['id'];
		unset($data['id']);
		$sitesModel = new SitesModel;
		$sitesModel->UpdateSiteInfoBySiteId($siteid, $data);
		return 'success';
	}
}
