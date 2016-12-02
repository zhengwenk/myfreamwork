<?php

namespace App\Model\Dashbords;

use Libs\Base\Model;

class Dashbord extends Model
{
	public function GetSitesListByUid($uid)
	{
		return $this->getDB('sites')
			->select('id as site_id', 'name', 'icon', 'domain', 'status', 'typeid', 'update_time')
			->where('uid',  $uid)
			->where('status', '<', 2)
			->orderBy('update_time', 'desc')
			->get()->toArray();
	}
}
