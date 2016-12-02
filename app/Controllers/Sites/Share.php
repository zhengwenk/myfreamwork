<?php

namespace App\Controllers\Sites;

use Libs\Base\Application;
use App\Model\Sites\Site as SitesModel;

class Share extends SiteBase
{
	public function __construct(Application $app)
	{
		$this->keys = array('site_id', 'site_share_pic', 'site_icon');
		$this->rules = array(
			'site_id' => 'required|integer',
			'site_share_pic' => 'max_len, 300',
			'site_icon' => 'max_len, 300'
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
		$sitesModel->UpdateSiteInfoBySiteId($siteid, $data);
		return 'success';
	}
}