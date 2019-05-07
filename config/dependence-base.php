<?php
// $configuration 变量由具体的应用文件提供
$container = new \Slim\Container($configuration);

//视图模板注入
$container['view'] = function ($container) {
    // 加载配置项
    $view = new \Slim\Views\Twig(__DIR__.'/../app/View', [
        'cache' => $container['settings']['DIR_CACHE'],
    ]);
    // 加载我们的SLIM扩展
    // TODO: 可以添加我们的自定义模板扩展
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));

    return $view;
};

//数据库ORM注入




//日志对象注入
use Monolog\Logger;
$container['logger'] = function($c) {
  $logger = new \Monolog\Logger('base');
  $file_handler = new \Monolog\Handler\StreamHandler($c['settings']['DIR_LOG']."/kytx-base.log",Logger::DEBUG);

  $processor = new Monolog\Processor\WebProcessor();
  $logger->pushHandler($file_handler);
  $logger->pushProcessor($processor);
  return $logger;
};
//sql query日志
$container['query-logger'] = function($c) {
  $logger = new \Monolog\Logger('query');
  $file_handler = new \Monolog\Handler\StreamHandler($c['settings']['DIR_LOG']."/kytx-query.log",Logger::DEBUG);

  $logger->pushHandler($file_handler);
  return $logger;
};


//错误处理Handler
use App\Common\Handlers\appError;
$container['errorHandler'] = function($c) {
  return new appError($c->get('logger'), $c['settings']['displayErrorDetails']);
};

//PHP 错误Handler
use App\Common\Handlers\phpError;
$container['phpErrorHandler'] = function($c) {
  return new phpError($c->get('logger'), $c['settings']['displayErrorDetails']);
};

