<?php
/**
 * @author zhengwenkai@erget.com
 * @date 16/11/24
 *
 */

$app->post('image/upload', 'UploadToQiNiu');
$app->get('image/list', 'UploadImageList');
$app->get('image/del', 'DelUploadResource');