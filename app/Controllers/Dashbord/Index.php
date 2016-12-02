<?php
namespace App\Controllers\Dashbord;

use Libs\Base\Application;
use Libs\Base\Controllers;
use App\Model\Dashbords\Dashbord as DashbordModel;


class Index extends Controllers
{
	public function __construct(Application $app)
	{
		$this->isReportLogin = true;
		parent::__construct($app);
	}

	public function go()
	{
		$DashbordModel = new DashbordModel;
		$info = $DashbordModel->GetSitesListByUid($this->uid);
		$count = 0;
		foreach ($info as &$value) {
			$value['pv'] = 0;
			if ($value['status'] == 1) {
				$count ++;
			}
		}

		return array(
			'onlineNum' => $count,
			'sites' => $info,
			'notify' => $this->getMessageList()
		);

	}

	public function getPv()
	{

	}

	public function getMessageList()
	{
		return array(
			array(
				"title"=>"系统升级想休息休息休息",
              	"url"=>"http://www.baidu.com"
			),
			array(
				"title"=>"系统升级想休息休息休息",
				"url"=>"http://www.baidu.com"
			),
			array(
				"title"=>"系统升级想休息休息休息",
				"url"=>"http://www.baidu.com"
			),
			array(
				"title"=>"系统升级想休息休息休息",
				"url"=>"http://www.baidu.com"
			),
			array(
				"title"=>"系统升级想休息休息休息",
				"url"=>"http://www.baidu.com"
			),
			array(
				"title"=>"系统升级想休息休息休息",
				"url"=>"http://www.baidu.com"
			)
		);
	}
}
