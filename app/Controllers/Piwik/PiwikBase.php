<?php

namespace App\Controllers\Piwik;

use App\Controllers\Base;
use Libs\Base\Application;

class PiwikBase extends Base
{
	protected $api;
	protected $token;
	protected $format = 'json';

	public function __construct(Application $app)
	{
		$this->isReportLogin = true;
		$this->api = env('PIWIK_API');
		$this->token = env('PIWIK_AUTH_TOKEN');
		$this->methods = array('CheckSiteUid', 'CheckConfig');
		parent::__construct($app);
	}

	public function CheckConfig()
	{
		if (!$this->token || !$this->api) {
			$this->msg = 'config empty';
		}
	}
}
