<?php
namespace App\Controllers\Sites;

use Libs\Base\Application;
use App\Model\Sites\Site as SitesModel;

class Copy extends SiteBase
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
		$SitesModel = new SitesModel;
		$res = $SitesModel->CopySite($data['id'], $this->uid);

		if (is_int($res) && $res >0) {
			return array('site_id'=> $res);
		} else {
			return $res;
		}
	}
}
