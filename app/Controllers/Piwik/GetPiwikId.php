<?php
namespace App\Controllers\Piwik;

use Libs\Base\Controllers;
use Libs\Base\Application;
use App\Model\Sites\SitePiwikMap as MapModel;


class GetPiwikId extends Controllers
{
	public function __construct(Application $app)
	{
		$this->keys = array('site_id');
		$this->rules = array('site_id' => 'required');
		parent::__construct($app);
	}

	public function go()
	{
		$map = new MapModel;
		return array('siteid' => $map->GetPiwikIdBySiteId($this->params['site_id']));
	}
}
