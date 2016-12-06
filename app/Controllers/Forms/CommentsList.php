<?php

namespace App\Controllers\Forms;

use App\Controllers\Base;
use Libs\Base\Application;
use App\Model\Forms\Comments as CommentsModel;
use App\Lang\Forms\Comments as CommentsLang;

class CommentsList extends Base
{
	public function __construct(Application $app)
	{
		$this->keys = array('site_id', 'limit');
		$this->rules = array(
			'site_id' => 'required|integer'
		);
		$this->isReportLogin = true;
		$this->methods = array('checkSiteUid');
		parent::__construct($app);
	}

	public function go()
	{
		$CommentsModel = new CommentsModel;
		$limit = isset($this->params['limit']) ? $this->params['limit'] : 10;
		$res = $CommentsModel->GetListBySiteId($this->params['site_id'], $limit);

		if (isset($res['pageNum'])) {
			return $res;
		} else {
			$this->code = 400;
			return CommentsLang::OPERATION_FAILURE;
		}
	}
}
