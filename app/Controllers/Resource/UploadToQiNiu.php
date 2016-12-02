<?php

namespace App\Controllers\Resource;

use Libs\Base\Controllers;
use Libs\Base\Application;
use Libs\Support\Str;
use App\Model\Resource\Resource as ResModel;
use App\Lang\Resource\Resource as ResLang;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Http\Client;

class UploadToQiNiu extends Controllers
{
	const QN_KEY_ACCESS = 'fCfviGmSeyM0qMDVSb3Doc-t6HRUiV000SjtoMbL';
	const QN_KEY_SECRET = 'XiWtfRDJHVid8DNrSgF5MPa4vhL2aPZTbalQQDkd';

	const BUCKET_IMAGE = 'larkimg';
	const BUCKET_DOMAIN = 'http://img.larkapp.com/';

	protected $imageData;
	protected $imageType;
	protected $modules = array('createSite');

	public function __construct(Application $app)
	{
		$this->keys = array('content', 'module');
		$this->rules = array(
			'content' => 'required',
			'module' => "required|contains,'createSite' 'gallery'"
		);
		$this->isReportLogin = true;
		$this->methods = array('checkContent');
		parent::__construct($app);
	}

	protected function checkContent()
	{
		list($prefix, $data) = explode(',', $this->params['content']);

		if (Str::contains('base64', $prefix) || Str::contains('image/', $prefix)) {
			$this->msg = ResLang::IMAGE_FORMAT_ERROR;
		} elseif (empty($data)) {
			$this->msg = ResLang::IMAGE_CONTENT_EMPTY;
		} else {
			list(, $type) = explode('/', $prefix);
			$type = str_replace(';base64', '', $type);
			$this->imageType = $type ? $type : 'jpeg';
			$this->imageData = base64_decode($data);
		}
	}

	public function go()
	{
		$auth = new Auth(self::QN_KEY_ACCESS, self::QN_KEY_SECRET);
		$token = $auth->uploadToken(self::BUCKET_IMAGE);
		$resourceId = uniqid('lark_qn_');
		$key = "{$this->params['module']}/lark_{$this->uid}/{$resourceId}.{$this->imageType}";
		$uploadMgr = new UploadManager();
		list($ret, $err) = $uploadMgr->put($token, $key, $this->imageData);

		if (isset($err)) {
			return ResLang::IMAGE_UPLOAD_QN_ERROR.'|'.$err;
		} else {
			$url = self::BUCKET_DOMAIN . $key;
			$data = array(
				"uid"    => $this->uid,
				"type"   => $this->imageType,
				"url"    => $url,
				"module" => $this->params['module']
			);
			$imageInfo      = Client::get($url . '?imageInfo')->json();
			$data['width']	= isset($imageInfo['width']) ? $imageInfo['width'] : 0;
			$data['height']	= isset($imageInfo['height']) ? $imageInfo['height'] : 0;

			$ResModel = new ResModel;
			$ResModel->Create($data);
			return array('r_path' => $url);
		}
	}
}
