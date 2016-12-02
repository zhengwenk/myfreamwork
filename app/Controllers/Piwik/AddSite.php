<?php
namespace App\Controllers\Piwik;

use Libs\Base\Controllers;
use Libs\Base\Application;
use App\Model\Sites\Site as SiteModel;
use App\Model\Sites\SitePiwikMap as MapModel;
use GuzzleHttp\Client;

class AddSite extends Controllers
{
	const API_METHOD = 'SitesManager.addSite';
	const API_SETACCESS_METHOD = 'UsersManager.setUserAccess';
	protected $api;
	protected $token;
	protected $format = 'json';

	public function __construct(Application $app)
	{
		$this->keys = array('siteid', 'code');
		$this->rules = array(
			'siteid' => 'required',
			'code' => 'required'
		);
		$this->api = env('PIWIK_API');
		$this->token = env('PIWIK_AUTH_TOKEN');
		$this->methods = array('CheckCode', 'CheckConfig');

		parent::__construct($app);
	}

	public function CheckCode()
	{
		if ($this->params['code'] != md5('larkapp')) {
			$this->msg = 'code 错误';
		}
	}

	public function CheckConfig()
	{
		if (!$this->token || !$this->api) {
			$this->msg = 'config empty';
		}
	}

	public function go()
	{
		$map = new MapModel;
		if ( true === $map->CheckSiteId($this->params['siteid'])) {
			$SiteModel = new SiteModel;
			$siteInfo = $SiteModel->GetSiteInfoBySiteId($this->params['siteid']);
			$httpClient = new Client;
			$response = $httpClient->request('GET', $this->api, array(
				'query' => array(
					'module' => 'API',
					'method' => static::API_METHOD,
					'siteName' => $siteInfo['name'],
					'urls' =>  'http://'.$siteInfo['domain'].'.larkapp.cn',
					'token_auth' => $this->token,
					'group' => $siteInfo['uid'],
					'siteSearch' => 1,
					'startDate' => date('Y-m-d h:i:s', time()),
					'format' => $this->format
				)
			));

			if (200 ==  $response->getStatusCode()) {
				$result = json_decode($response->getBody(), true);
				if (is_array($result) && key_exists('value', $result)) {
					$map->Add($this->params['siteid'], $result['value']);
				}
				return 'suceess';
			} else {
				return 'failure';
				//log
			}
		} else {
			return 'failure';
		}
	}
}
