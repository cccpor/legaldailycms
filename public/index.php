<?php
global $g_boot_time;
$g_boot_time = microtime(true);

$app_config = require(dirname(__FILE__) . '/../config/boot.php');
require $app_config['QEEPHP_DIR'] . '/library/q.php';
require $app_config['APP_DIR'] . '/myapp.php';

$ret = MyApp::instance($app_config)->dispatching();
return $ret;