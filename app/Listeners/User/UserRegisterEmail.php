<?php

namespace App\Listeners\User;

use App\Events\User\UserRegister;
use Libs\Base\Application;

class UserRegisterEmail
{
	protected $app;
	/**
	 * Create the event listener.
	 */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * Handle the event.
	 *
	 * @param  UserRegister  $event
	 * @return void
	 */
	public function handle(UserRegister $event)
	{
		$view = <<<EOF
		尊敬的用户，您好！
			这封信是由 十分建站 发送的。您收到这封邮件，是由于在 十分建站 进行了新用户注册，或用户修改 Email 使用 了这个邮箱地址。
		如果您并没有访问过 十分建站，或没有进行上述操作，请忽略这封邮件。

		----------------------------------------------------------------------
			帐号激活说明
		----------------------------------------------------------------------

			如果您是 十分建站 的新用户，或在修改您的注册 Email 时使用了本地址，我们需 要对您的地址有效性进行验证以避免垃圾邮件或地址被滥用。
		您只需点击下面的链接即可激活您的帐号：
		%s
		(如果上面不是链接形式，请将该地址手工粘贴到浏览器地址栏再访问)
		感谢您的访问，祝您使用愉快！

	此致
	十分建站 团队
EOF;
		$userInfo = $event->userModel->getUserInfo();
		$email = $userInfo['email'];
		$url = $this->CreateVerifyUrl($email, $userInfo['uid'], $userInfo['pwd']);

		$this->app['mailer']->raw(sprintf($view, $url), function($message) use($email){
			$message->to($email)->subject('十分建站账号激活邮件');
		});
	}

	protected function CreateVerifyUrl($email, $uid, $pwd)
	{
		$url = 'http://'.env('APP_FEHOST').'/login/active/?token=';
		$token = array(
			'email' => $email,
			'uid' => $uid,
			'pwd' => $pwd,
			'time' => time()
		);
		$token = json_encode($token);
		$token = $this->app['encrypter']->encrypt($token);
		return $url.$token;
	}
}
