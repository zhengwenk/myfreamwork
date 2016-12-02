<?php

namespace App\Lang\Sites;

use App\Lang\Base;

class Site extends Base
{
	const TPLID_INVALID = '无效的模板id';
	const SITEID_EMPTY = '站点id不能为空';
	const NO_SITE_ACCESS = '没有权限操作该站点';
	const SITE_NO_EXIST = '站点查询失败';
	const COPY_FAILURE = '站点复制失败';
}