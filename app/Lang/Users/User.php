<?php

namespace App\Lang\Users;

use App\Lang\Base;

class User extends Base
{
	const ACCOUNT_EXIST_CODE = '14002';
	const REG_UNKONW_ERROR = '注册失败';
	const LOGIN_UNKONW_ERROR = '登陆失败';
	const LOGIN_ACCOUNT_EMPTY_CODE = '12002';
	const LOGIN_ACCOUNT_PWD_NOMATCH_CODE = '14001';
	const LOGIN_ACCOUNT_NOTEXIST_CODE = '12001';
	const VIERIFY_TIMEOUT = '验证邮件已经过期';
	const VIERIFY_CODE_ERROR = '非法的验证邮件';
	const MODIFY_PASSWORD_FAILURE = '修改密码失败';

	public static $userCode = array(
		'12001' => '账号不存在',
		'12002' => '账号不能为空',
		'14001' => '账号密码不匹配',
		'14002' => '账号已经存在',
	);
}
