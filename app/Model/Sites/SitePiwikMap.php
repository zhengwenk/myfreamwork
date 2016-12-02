<?php
namespace App\Model\Sites;

use Libs\Base\Model;

class SitePiwikMap extends Model
{
	public $table = 'sites_piwik_map';

	public function Add($siteId, $piwikId)
	{
		return $this->getDB()->insert(array('siteId' => $siteId, 'piwikid'=> $piwikId));
	}

	public function GetPiwikIdBySiteId($siteId)
	{
		$res =  $this->getDB()->where('siteid', $siteId)->first();
		return (is_array($res) && key_exists('piwikid', $res)) ? $res['piwikid'] : 0;
	}

	public function CheckSiteId($siteId)
	{
		$res = $this->getDB()->where('siteid', $siteId)->first();
		return (is_array($res) && key_exists('siteid', $res)) ? false : true;
	}
}
