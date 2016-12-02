<?php

namespace App\Model\Sites;

use Libs\Base\Model;

class SiteConfig extends Model
{
	public $table = 'sites_config';

	public function SetSiteConfigBySiteId($siteId, $data)
	{
		$res = $this->GetSiteConfigBySiteId($siteId);
		if (!empty($res) && key_exists('site_config', $res)) {
			return $this->getDB()->where('siteid', $siteId)->update($data);
		} else {
			$data['add_time'] = date('Y-m-d H:i:s', time());
			return $this->getDB()->insert($data);
		}
	}

	public function GetSiteConfigBySiteId($siteId)
	{
		return $this->getDB()->select('config as site_config')->where('siteid', $siteId)->first();
	}

}
