<?php
if ($_GET['anctmd'] !== 'tvns-iolj-scyu-isvkih-5vf7qwc') {
    die();
}

`echo ' ' > /opt/TeamCity/buildAgent/work/skiliks-unit/protected/views/static/applicationcache/preload_images.php`;
`chmod 777 /opt/TeamCity/buildAgent/work/skiliks-unit/protected/views/static/applicationcache/preload_images.php`;
`cd /opt/TeamCity/buildAgent/work/skiliks-unit`;
`phing -Dstage=test`;
`./yiic createlistofpreloadedfiles`;
`./yiic migrate`;
