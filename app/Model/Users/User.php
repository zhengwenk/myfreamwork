<?php

namespace App\Model\Users;

use Libs\Base\Model;
use App\Lang\Users\User as UserLang;
use Exception;

class User extends Model
{
	protected $table = 'user';
	protected $userInfo = array();

	public function Register($email, $password, $nickname, $ip, $account = '', $mobile = '')
	{

		if (false === $this->CheckUserExist($email)) {
			$passwordHash = $this->MakePassword($password);
			$data = array(
				'email' => $email,
				'password' => $passwordHash['password'],
				'nickName' => $nickname,
				'salt' => $passwordHash['salt'],
				'regdate' => date('Y-m-d H:i:s', time()),
				'regip' => $ip,
				'lastloginip' => $ip
			);

			if ($account) {
				$data['account'] = $account;
			}

			if ($mobile) {
				$data['mobile'] = $mobile;
			}

			$uid = $this->getDB()->insertGetId($data);
			$this->userInfo = array(
				'email' => $email,
				'uid' => $uid,
				'pwd' => $password
			);
			return true;

		} else {
			return UserLang::ACCOUNT_EXIST_CODE;
		}
	}

	protected function MakePassword($password)
	{
		$salt = substr(md5($password), 0, 6);
		return  array(
			'password' => md5($password.$salt),
			'salt' => $salt
		);
	}

	protected function CheckUserExist($email)
	{
		$user = $this->getDB()->select('email')
			->where('email', $email)
			->first();
		return ($user && !key_exists('email', $user) && $email == $user['email']) ? true : false;
	}

	public function Login($email, $password, $ip, $uid='', $account = '', $mobile = '')
	{
		if ($email) {
			$userInfo = $this->getDB()->select('uid', 'email', 'nickName', 'mobile', 'password', 'emailstatus', 'salt')->where('email', $email)->first();
		} elseif($uid) {
			$userInfo = $this->getDB()->select('uid', 'email', 'nickName', 'mobile', 'password', 'emailstatus', 'salt')->where('uid', '=', $uid)->first();
		} elseif ($account) {
			$userInfo = $this->getDB()->select('uid', 'email', 'password', 'emailstatus', 'salt')->where('account', $account)->first();
		} elseif ($mobile) {
			$userInfo = $this->getDB()->select('uid', 'email', 'password', 'emailstatus', 'salt')->where('mobile', $mobile)->first();
		} else {
			return UserLang::LOGIN_ACCOUNT_EMPTY_CODE;
		}

		if ($userInfo) {
			if ($this->ComparePassword($password, $userInfo['password'], $userInfo['salt'])) {
				$this->getDB()->where('uid', '=', $userInfo['uid'])
					->update(array(
						'lastloginip' => $ip,
					));
				unset($userInfo['password'], $userInfo['salt']);
				return  $userInfo;
			} else {
				return UserLang::LOGIN_ACCOUNT_PWD_NOMATCH_CODE;
			}

		} else {
			return UserLang::LOGIN_ACCOUNT_NOTEXIST_CODE;
		}
	}

	public function CheckPassword($uid, $password)
	{
		$userInfo = $this->getDB()->select('uid', 'password', 'emailstatus', 'salt')->where('uid', '=', $uid)->first();
		if (is_array($userInfo) && key_exists('password', $userInfo)) {
			return $this->ComparePassword($password, $userInfo['password'], $userInfo['salt']);
		} else {
			return false;
		}
	}

	protected function ComparePassword($inputPwd, $dbPwd, $salt)
	{
		return md5($inputPwd.$salt) == $dbPwd ? true : false;
	}

	public function UpdatePassword($uid, $newpassword)
	{
		$pwdhash = $this->MakePassword($newpassword);

		return $this->getDB()->where('uid', $uid)->update(
			array(
				'password' => $pwdhash['password'],
				'salt' => $pwdhash['salt']
			)
		);
	}

	public function UpdateEmailStatus($email)
	{
		return $this->getDB()->where('email', $email)->update(array('emailstatus'=>1));
	}

	public function getUserInfo()
	{
		return $this->userInfo;
	}

	public function createToken($uid, $pwd, $encrypter)
	{
		$token = json_encode(
			array(
				'uid'=> $uid,
				'pwd' => $pwd,
				'time' => time()
			)
		);
		return  $encrypter->encrypt($token);
	}

	public function UpdateUserInfo($uid, $data)
	{
		if (key_exists('uid', $data) || key_exists('password', $data)) {
			throw new Exception(UserLang::UPDATE_DATA_NOTALLOW);
		}

		return $this->getDB()->where('uid', $uid)->update($data);
	}
}
