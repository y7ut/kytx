<?php
$configuration = require 'configuration.php';//引入设置的配置文件
require __DIR__ . '/../dependence-base.php';//引入controller配置文件

//修改默认 重置404 页面的逻辑
$container['notFoundHandler'] = function ($c) {
  return function ($request, $response) use ($c) {
    return $c->view->render($response,  'Admin/layout/404.html');
  };
};
