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
}
