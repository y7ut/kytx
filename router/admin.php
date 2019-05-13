<?php

use App\Common\Middleware\AuthMiddleware;
use App\Controller\Admin\AuthController;
use App\Controller\Admin\BoardController;

$app->group('/admin', function() {
    $this->get('', BoardController::class.':home')->setName('admin.board');//获取节点列表
})->add(new AuthMiddleware($app->getContainer()));

$app->group('/authentication', function() {
    $this->post('/auth', AuthController::class.':auth')->setName('admin.login');//登录请求
    $this->get('/logout', AuthController::class.':logout')->setName('admin.logout');//退出登录
    $this->get('/login', AuthController::class.':login')->setName('admin.loginPage');//登录页面
});

