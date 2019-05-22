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
    $view->addExtension(new  App\Common\TwigExtension\AdminExtension($container['auth'],$container['validator']));
    $view->addExtension(new Knlv\Slim\Views\TwigMessages(
        new Slim\Flash\Messages()
    ));
    // Add the validator extension
    $view->addExtension(
        new Awurth\SlimValidation\ValidatorExtension($container['validator'])
    );

    return $view;
};

// 前段提示
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

//session控制
$container['session'] = function () {
    return new \SlimSession\Helper;
};

//用户鉴权
$container['auth'] = function ($container){
    return new \App\Common\Auth\AdminAuth($container['session']);
};

$defaultMessages = [
    'length' => '长度需要在{{minValue}}和{{maxValue}}之间',
    'email' => '{{name}}不是一个合法的邮箱',
    'notBlank' => '这是一个必填项',
    'equals' => '两次输入的密码不一致，请重试',
    'numeric' => '{{name}}应该是一个数字',
    'notempty' => '内容不能为空'
];

// 表单验证
$container['validator'] = function () use ($defaultMessages) {
    return new Awurth\SlimValidation\Validator(true, $defaultMessages);
};


use App\Common\TwigExtension\AdminExtension;
use Illuminate\Events\Dispatcher;

//数据库ORM注入
$container['db'] = function ($c) {
    $connections = $c->get('settings')['mysql'];
    // 初始化Eloquent ORM
    $capsule = new \Illuminate\Database\Capsule\Manager();

    $connectionList = [];

    foreach ($connections as $name => $settings) {
        $capsule->addConnection($settings, $name);
        // 记录添加的连接名称
        $connectionList[] = $name;
    }
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    $dispatcher = new Dispatcher();
    // 我们需要将数据库的fetch 模式改为 FETCH_ASSOC
    // 参考 https://github.com/laravel/framework/issues/17557
    $dispatcher->listen(\Illuminate\Database\Events\StatementPrepared::class, function ($event) {
        $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
    });

    // 提取配置中的query-logger 对象
    $query_log = $c->get('query-logger');
    // 给每个connection 对象附加监听事件
    foreach ($connectionList as $connectionName) {
        // 需要设置dispatcher才可以实现日志的监听
        $connectionObj = $capsule->connection($connectionName);
        $connectionObj->setEventDispatcher($dispatcher);

        // 根据设置处理SQL日志记录
        if ($c->get('settings')['enableQueryLog'] ?? false) {
            $connectionObj->listen(function ($queryExecuted) use ($query_log) {
                $query_log->info($queryExecuted->sql, ['bindings' => $queryExecuted->bindings, 'time' => $queryExecuted->time]);
            });
        }
    }

    return $capsule;
};



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

//修改默认 重置404 页面的逻辑
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c->view->render($response,  'Admin/layout/404.html');
    };
};
