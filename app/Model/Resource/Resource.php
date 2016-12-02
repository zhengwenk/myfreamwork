<?php

namespace App\Model\Resource;

use Libs\Base\Model;

class Resource extends Model
{
	protected $table = 'resource';

	public function Create($data)
	{
		$data['addtime'] = date('Y-m-d H:i:s', time());
		return $this->getDB()->insert($data);
	}

	public function Del($uid, $resourceId)
	{
		return $this->getDB()->where('id', $resourceId)
			->where('uid', $uid)
			->update(array('status'=>0));
	}

	public function GetImageListByUid($uid, $limit = 10)
	{
		return $this->getDB()->select('id as imgid', 'url', 'width', 'height')
			->where('uid', $uid)
			->where('status', 1)
			->orderBy('addtime', 'desc')
			->paginate($limit)
			->toArray();
	}
}
