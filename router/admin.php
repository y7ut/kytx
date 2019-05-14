<?php

use App\Common\Middleware\AuthMiddleware;
use App\Controller\Admin\AuthController;
use App\Controller\admin\BannerController;
use App\Controller\Admin\BoardController;

$app->group('/admin', function() {
    $this->get('', BoardController::class.':home')->setName('admin.board');//获取节点列表
    $this->group('/banner', function() {
        $this->get('', BannerController::class.':index')->setName('admin.bannerTable');//轮播图列表页面
    });
    $this->group('/user', function() {
        $this->get('', AuthController::class.':info')->setName('admin.userEditPage');//个人信息页面
        $this->put('', AuthController::class.':infoEdit')->setName('admin.userEdit');//修改个人信息
        $this->put('/avatar', AuthController::class.':avatarEdit')->setName('admin.userAvatarEdit');//修改个人头像
    });
})->add(new AuthMiddleware($app->getContainer()));

$app->group('/authentication', function() {
    $this->post('/auth', AuthController::class.':auth')->setName('admin.login');//登录请求
    $this->get('/logout', AuthController::class.':logout')->setName('admin.logout');//退出登录
    $this->get('/login', AuthController::class.':login')->setName('admin.loginPage');//登录页面
});

