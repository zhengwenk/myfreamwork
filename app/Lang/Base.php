<?php
namespace App\Lang;

class Base
{
	const ERROR_CODE = '400';
	const NEED_LOGIN = '你还未登陆或者登陆状态已经失效，请重新登陆';
	const TOKEN_PARSER_ERROR = 'token解析失败';
	const PARAMS_EMPTY = '请求参数不允许全部为空';
	const UPDATE_DATA_NOTALLOW = '更新字段不被允许';
	const HTTP_REQUEST_ERROR = '请求失败-';

	public static $baseCode = array();
}