<?php

namespace App\Model\Sites;

use Libs\Base\Model;
use App\Lang\Sites\Site as SiteLang;

class Site extends Model
{
	protected $table = 'sites';

	public function Create($uid, $nickname, $tplid)
	{
		return $this->getDB()->insertGetId(array(
			'uid' => $uid,
			'name' => $nickname.'的站点',
			'tplid' => $tplid,
			'domain' => time().$uid,
			'create_time' => date('Y-m-d H:i:s', time())
		));
	}

	public function GetSiteBySiteId($siteid)
	{
		return $this->getDB()->select('uid')->where('id', $siteid)->first();
	}

	public function UpdateSiteInfoBySiteId($siteid, $data)
	{
		return  $this->getDB()->where('id', $siteid)->update($data);
	}

	public function UpdateDomainBySiteId($siteid, $domain)
	{
		if (false === $this->checkDomain($siteid, $domain)) {
			return  $this->UpdateSiteInfoBySiteId($siteid, array('domain'=> $domain));

		} else {
			return 'domain exist';
		}
	}

	public function checkDomain($siteid, $domain)
	{
		$data = $this->getDB()->select('id', 'domain')->where('domain', $domain)->first();

		if ($data && key_exists('domain', $data)
			&& $data['domain'] == $domain
			&& $data['id'] != $siteid) {

			return true;
		} else {
			return false;
		}
	}

	public function GetSiteInfoBySiteId($siteId)
	{
		return $this->getDB()
			->select(
				'id',
				'uid',
				'name',
				'tplid',
				'desc',
				'keyword',
				'typeid',
				'domain',
				'status',
				'share_pic',
				'icon',
				'online_num'
			)->where('id', $siteId)->first();
	}

	public function copySite($siteId, $uid)
	{
		$res =  $this->getDB()
			->select(
				'name',
				'tplid',
				'desc',
				'keyword',
				'typeid',
				'share_pic',
				'icon'
			)->where('id', $siteId)->first();

		if (key_exists('name', $res)) {
			$res['uid'] = $uid;
			$res['name'] = $res['name'].'(的副本)';
			$res['domain'] = time().$uid;
			$siteId = $this->getDB()->insertGetId($res);
			if ($siteId > 0) {
				return $siteId;
			} else {
				return SiteLang::COPY_FAILURE;
			}
		} else {
			return SiteLang::SITE_NO_EXIST;
		}
	}

	public function delSiteBySiteId($siteId)
	{
		return $this->getDB()->where('id', $siteId)->update(array('status' => 2));
	}

	public function OnlineBySiteId($siteId, $data)
	{
		if (empty($data)) {
			$res =  $this->getDB()->where('id', $siteId)->increment('online_num', 1, array('status' => 1));
		} else {
			$data['status'] = 1;
			$res =  $this->getDB()->where('id', $siteId)->increment('online_num', 1, $data);
		}

		if ($res == 1) {
			return true;
		} else {
			return false;
		}
	}

	public function OfflineBySiteId($siteId)
	{
		return $this->getDB()->where('id', $siteId)->update(array('status'=>0));
	}

	public function CheckSiteMaster($siteId, $uid)
	{
		$site = $this->GetSiteBySiteId($siteId);
		return $this->Compare($uid, $site, 'uid');
	}

	public function GetSiteIdByDomain($domain)
	{
		$res = $this->getDB()->select('id')->where('domain', $domain)->first();
		return isset($res['id']) ? $res['id'] : 0;
	}
}
