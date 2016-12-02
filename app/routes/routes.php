<?php
/**
 * @author zhengwenkai@erget.com
 * @date 16/11/30
 *
 */

$app->group(['namespace' => 'App\Controllers\User'], function ($app) {
	require 'user.php';
});

$app->group(['namespace' => 'App\Controllers\Sites'], function ($app) {
	require 'site.php';
});

$app->group(['namespace' => 'App\Controllers\Dashbord'], function ($app) {
	require 'dashbord.php';
});

$app->group(['namespace' => 'App\Controllers\Resource'], function ($app) {
	require 'resource.php';
});

$app->group(['namespace' => 'App\Controllers\Piwik'], function ($app) {
	require 'piwik.php';
});