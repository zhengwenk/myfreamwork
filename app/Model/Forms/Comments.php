<?php

namespace App\Model\Forms;

use Libs\Base\Model;

class Comments extends Model
{
	protected $table = 'forms_comments';

	public function Add($siteId, $data)
	{
		$data['siteid'] = $siteId;
		return $this->getDB()->insert($data);
	}

	public function GetListBySiteId($siteId, $limit = 10)
	{
		return $this->getDB()->select('tag', 'name', 'email', 'qq', 'phone', 'comments')
			->where('siteid', $siteId)
			->orderBy('addtime', 'desc')
			->paginate($limit)
			->toArray();
	}
}
