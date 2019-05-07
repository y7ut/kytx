<?php
//定义正式环境和测试环境
define ('ENV_DEVELOPMENT', true);

//设定默认时区
date_default_timezone_set('Asia/Shanghai');

if (php_sapi_name()==='fpm-fcgi'){
  $_SERVER['SCRIPT_NAME'] = '/index.php';
}
