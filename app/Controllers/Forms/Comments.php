<?php

namespace App\Controllers\Forms;

use Libs\Base\Controllers;
use Libs\Base\Application;
use App\Model\Forms\Comments as CommentsModel;
use App\Lang\Forms\Comments as CommentsLang;

class Comments extends Controllers
{
	public function __construct(Application $app)
	{
		$this->keys = array('site_id', 'tag', 'name', 'email', 'phone', 'comments');
		$this->rules = array(
			'site_id' => 'required|integer',
			'tag' => 'required|max_len,4|min_len,2',
			'name' => 'max_len,40',
			'email' => 'valid_email|max_len,320',
			'phone' => 'max_len,11',
			'qq' => 'max_len,12|min_len,5',
			'comments' => 'max_len,140'
		);
		parent::__construct($app);
	}

	public function go()
	{
		$siteId = $this->params['site_id'];
		unset($this->params['site_id']);
		if (count($this->params) == 0) {
			return CommentsLang::PARAMS_EMPTY;
		} else {
			$CommentsModel = new CommentsModel;
			return (true === $CommentsModel->Add($siteId, $this->params)) ?
				CommentsLang::OPERATION_SUCCESS : CommentsLang::OPERATION_FAILURE;
		}
	}
}
