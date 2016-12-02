<?php
/**
 * @author zhengwenkai@erget.com
 * @date 16/11/30
 *
 */

$app->get('piwik/addsite', 'AddSite');

$app->get('Piwik/Getinfo', 'GetPiwikId');

$app->get('Statistics/getPv', 'GetPv');
$app->get('Statistics/getUv', 'GetUv');