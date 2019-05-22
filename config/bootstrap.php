<?php
// 引入环境配置
require __DIR__ . '/environment.php';
// autoload
require __DIR__.'/../vendor/autoload.php';//自动加载的脚本

// 依赖注入
if (defined ('ENV_DEVELOPMENT')) {
  // 测试环境
  define ('CONF_DIR', '/development');
}
else {
  define ('CONF_DIR', '/production');
}
require __DIR__ . CONF_DIR . '/dependence.php';



$app = new \Slim\App($container);

// Start PHP session
$app->add(new \Slim\Middleware\Session([
    'name' => 'kytx_session',
    'autorefresh' => true,
]));

$app->getContainer()['db'];




require __DIR__ . '/../router/home.php';
require __DIR__ . '/../router/admin.php';
