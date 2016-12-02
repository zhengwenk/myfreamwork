<?php
/**
 * @author zhengwenkai@erget.com
 * @date 16/11/21
 *
 */

$app->get('Site/add', 'Create');
$app->post('Site/setBaseInfo', 'BaseInfo');
$app->post('Site/setDomain', 'Domain');
$app->post('Site/setShare', 'Share');
$app->get('Site/online', 'Online');
$app->get('Site/offline', 'Offline');

$app->get('Site/getSiteInfo', 'GetSiteInfo');
$app->get('Site/copySite', 'Copy');
$app->get('Site/delSite', 'Del');
$app->get('Site/getSiteid', 'GetSiteId');

$app->post('SiteConfig/set', 'SiteConfig');
$app->get('SiteConfig/get', 'GetSiteConfig');
