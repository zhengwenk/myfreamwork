<?php

namespace App\Listeners\Sites;

use App\Events\Sites\SiteCreate;
use Libs\Base\Application;
use GuzzleHttp\Client;

class SiteCreatePiwik
{
	public $app;
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	public function handle(SiteCreate $event)
	{
		$siteId = $event->siteid;
		$code = md5('larkapp');
		$httpClient = new Client;
		$response = $httpClient->request('GET', 'http://'.env('APP_HOST').'/piwik/addsite', array(
			'query' => array(
				'siteid' => $siteId,
				'code' => $code
			)
		));
	}
}
