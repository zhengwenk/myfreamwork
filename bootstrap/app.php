<?php
/**
 * @author zhengwenkai@erget.com
 * @date 16/11/9
 *
 */

require_once __DIR__.'/../vendor/autoload.php';

try {
	(new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
	exit($e->getMessage());
}

$app = new Libs\Base\Application(
	realpath(__DIR__.'/../app/')
);

$app->withFacades();

$app->register('App\Providers\EventServiceProvider');

require __DIR__.'/../app/routes/routes.php';

return $app;
