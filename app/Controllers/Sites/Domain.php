<?php

namespace App\Controllers\Sites;

use Libs\Base\Application;
use App\Model\Sites\Site as SitesModel;

class Domain extends SiteBase
{
	public function __construct(Application $app)
	{
		$this->keys = array('site_id', 'site_domain');
		$this->rules = array(
			'site_id' => 'required|integer',
			'site_domain' => 'required|max_len, 32|min_len, 4|alpha_dash'
		);
		$this->methods = array('checkSiteUid');
		parent::__construct($app);
	}

	public function go()
	{
		$data = $this->removeDataPrefix('site_', $this->params);
		$SitesModel = new SitesModel;
		$res = $SitesModel->UpdateDomainBySiteId($data['id'], $data['domain']);

		if ($res == 1 || $res == 0) {
			return 'success';
		} else {
			return (string) $res;
		}
	}
}
