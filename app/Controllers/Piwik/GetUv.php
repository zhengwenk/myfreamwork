<?php

namespace App\Controllers\Piwik;

use Libs\Base\Application;
use App\Model\Sites\SitePiwikMap as MapModel;
use App\Lang\Piwik\Piwik as PiwikLang;
use GuzzleHttp\Client as httpClient;

class GetUv extends PiwikBase
{
	const API_METHOD = 'Actions.get';

	public function __construct(Application $app)
	{
		$this->keys = array('app_id', 'start_date', 'end_date');
		$this->rules = array(
			'app_id' => 'required',
			'start_date' => 'required',
			'end_date' => 'required'
		);
		parent::__construct($app);
	}

	public function go()
	{
		$MapModel = new MapModel;
		$piwikId = $MapModel->GetPiwikIdBySiteId($this->params['app_id']);
		$start = date('Y-m-d',strtotime($this->params['start_date']));
		$end = date('Y-m-d',strtotime($this->params['end_date']));

		if ($piwikId > 0) {
			$httpClient = new httpClient;
			$query = array(
				'module' => 'API',
				'idSite' => $piwikId,
				'period' => 'day',
				'date' => $start.','.$end,
				'format' => $this->format,
				'token_auth' => $this->token,
				'method' => static::API_METHOD,
				'showColumns' => 'nb_uniq_pageviews'
			);

			$response = $httpClient->request('GET', $this->api, array(
				'query' => $query
			));

			if (200 ==  $response->getStatusCode()) {
				$res  = json_decode($response->getBody(), true);
				$data = array(
					'totalUv' => array('count' => 0),
					'dateUv' => array()
				);
				$total = 0;

				if (isset($res[$start])) {
					foreach ($res as $index => $value) {
						if (!empty($value)) {
							$total += (int) $value;
							$data['dateUv'][] = array(
								'record_date' => $index,
								'count' => $value
							);
						} else {
							$data['dateUv'][] = array(
								'record_date' => $index,
								'count' => 0
							);
						}
					}
					$data['totalPv']['count'] = $total;
					return $data;
				} else {
					$this->code = 400;
					return PiwikLang::QUERY_UV_ERROR;
				}
			} else {
				$this->code = 400;
				return PiwikLang::HTTP_REQUEST_ERROR.$response->getStatusCode();
			}
		} else {
			$this->code = 400;
			return PiwikLang::QUERY_PIWIKID_ERROR;
		}
	}
}
