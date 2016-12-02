<?php
/**
 * @author zhengwenkai@erget.com
 * @date 16/11/11
 *
 */

return array(

	'fetch' => PDO::FETCH_ASSOC,

	'default' => env('DB_CONNECTION', 'mysql'),

	'connections' => array(
		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => env('DB_HOST', 'localhost'),
			'port'      => env('DB_PORT', 3306),
			'database'  => env('DB_DATABASE', 'forge'),
			'username'  => env('DB_USERNAME', 'forge'),
			'password'  => env('DB_PASSWORD', ''),
			'charset'   => env('DB_CHARSET', 'utf8'),
			'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
			'prefix'    => env('DB_PREFIX', ''),
			'timezone'  => env('DB_TIMEZONE', '+08:00'),
			'strict'    => env('DB_STRICT_MODE', false),
		)
	)
);
