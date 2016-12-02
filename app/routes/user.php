<?php
/**
 * @author zhengwenkai@erget.com
 * @date 16/11/10
 *
 */

$app->get('test', 'Test');
$app->post('User/reg', 'Register');
$app->get('User/login', 'Login');
$app->get('User/activeEmail', 'Verify');
$app->get('User/get', 'GetUserInfo');
$app->get('User/set', 'UpdateUserInfo');
$app->get('User/modifypwd', 'ModifyPassword');
