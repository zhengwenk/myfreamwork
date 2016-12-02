<?php

namespace Libs\Base;

use Exception;
use App\Lang\Base as BaseLang;
use App\Model\Users\User as UserModel;

abstract class Controllers
{
	protected $app;
	protected $request;
	protected $response;
	protected $validate;
	protected $encrypter;
	protected $returnData;
	protected $cookiesKey = array();
	protected $headersKey = array();
	protected $keys = array();
	protected $cookies = array();
	protected $headers = array();
	protected $params = array();
	protected $methods = array();
	protected $rules = array();
	protected $filter = array();
	protected $userInfo = array();
	protected $uid = 0;
	protected $isReportLogin = false; // 是否返回没有登陆
	protected $msg;
	protected $code = 0;
	protected $outPut;
	protected $data;


	public function __construct(Application $app)
	{
		$this->app = $app;
		$this->request  = $app['request'];
		$this->response = $app['response'];
		$this->validate = $app['validate'];
		$this->encrypter = $app['encrypter'];
		$this->outPut = env('APP_OUTPUT', '');
	}

	public function init()
	{
		foreach (array('prepareParam', 'setParams', 'checkLogin', 'doMethod') as $m) {
			call_user_func(array($this, $m));
			if ($this->msg) {
				break;
			}
		}
	}

	protected function prepareParam()
	{
		if (!is_array($this->keys) || !is_array($this->cookiesKey) || !is_array($this->headersKey)) {
			throw new Exception ("Controller->prepareParame error:keys, cookies, headers must be an array");
		}

		$this->cookiesKey[] = 'token';
		$this->headersKey[] = 'token';
	}

	public function run()
	{
		$this->init();

		if (!$this->msg) {

			$content = $this->go();

			if (is_null($content)) {
				$content = $this->data;
			}

			if (is_null($content)) {
				throw new Exception('Controllers->run:  $content is NULL');
			}

		} else {
			$this->code = BaseLang::ERROR_CODE;
			$content = $this->msg;
		}

		if ($this->outPut == 'Json' || is_array($content)) {
			$content = json_encode(array(
				'code' => $this->code,
				'data' => $content
			));

			$this->response->headers->set('Access-Control-Allow-Origin','*');
			$this->response->headers->set('Content-type', 'application/json; charset=utf-8');
			$this->response->headers->set('Access-Control-Allow-Headers', 'X-Request-With, Content-Type,token');
		}

		$this->response->setContent($content);
		return $this->response;
	}

	protected function setParams()
	{
		if (!empty($this->keys)) {
			$params = array();
			array_walk($this->keys, function ($v) use(&$params) {
				$params[$v] = $this->request->get($v, '');
			});

			if (!empty($this->rules) || !empty($this->filter)) {
				$this->validate->validation_rules($this->rules);
				$this->validate->filter_rules($this->filter);
				$validated_data = $this->validate->run($params);
				if($validated_data === false) {
					$this->msg = $this->validate->get_readable_errors()[0];
					return ;
				} else {
					$this->params = $validated_data;
				}
			} else {
				$this->params = $params;
			}

			if (empty($this->params = array_filter($this->params))) {
				$this->msg = BaseLang::PARAMS_EMPTY;
				return ;
			}
		}

		if (!empty($this->cookiesKey)) {
			$cookies = array();
			array_walk($this->cookiesKey, function ($v) use(&$cookies) {
				$cookies[$v] = $this->request->cookies->get($v, '');
			});

			$this->cookies = $cookies;
		}

		if (!empty($this->headersKey)) {
			$headers = array();
			array_walk($this->headersKey, function ($v) use(&$headers) {
				$headers[$v] = $this->request->headers->get($v, '');
			});

			$this->headers = $headers;
		}
	}

	protected function doMethod()
	{
		if (!empty($this->methods)) {
			foreach ($this->methods as $value) {
				if (method_exists($this, $value)) {
					call_user_func(array($this, $value));
					if ($this->msg) {
						break;
					}
				} else {
					throw new Exception("Controller->doMethod error: method {$value} not exist");
				}
			}
		}
	}

	// @todo 做成中间件
	protected function checkLogin()
	{
		$token = $msg = '';
		if (key_exists('token', $this->headers) && !empty($this->headers['token'])) {
			$token = $this->headers['token'];
		} elseif (key_exists('token', $this->cookies) && !empty($this->cookies['token'])) {
			$token = $this->cookies['token'];
		} else {

		}

		if (!empty($token)) {
			$token = json_decode($this->encrypter->decrypt($token), true);
			if (isset($token['uid']) && isset($token['pwd']) && isset($token['time'])) {
				if ((time() - $token['time']) < 3600*24*30) {
					$userModel = new UserModel();
					$userInfo = $userModel->Login(
						'',
						$token['pwd'],
						$this->request->getClientIp(),
						$token['uid']
					);
					if (is_array($userInfo) && key_exists('uid', $userInfo)) {
						$this->userInfo = $userInfo;
						$this->uid = $userInfo['uid'];
					}
				}
			} else {
				$this->msg = BaseLang::TOKEN_PARSER_ERROR;
			}
		}

		if (! $this->uid && true === $this->isReportLogin) {
			$this->msg = BaseLang::NEED_LOGIN;
		}
	}

	abstract protected function go();
}
