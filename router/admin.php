<?php

use App\Common\Middleware\AuthMiddleware;
use App\Controller\Admin\AuthController;
use App\Controller\admin\BannerController;
use App\Controller\Admin\BoardController;
use App\Controller\admin\JobController;
use App\Controller\admin\NewsController;
use App\Controller\admin\UserController;

$app->group('/admin', function() {
    $this->get('', BoardController::class.':home')->setName('admin.board');//获取节点列表
    $this->group('/banners', function() {
        $this->get('', BannerController::class.':index')->setName('admin.bannerTable')->add(new \App\Common\Middleware\PaginationMiddleware($this->getContainer()));//轮播图列表页面
        $this->get('/new', BannerController::class.':create')->setName('admin.bannerCreate');//轮播图添加页面
        $this->get('/edit/{id}', BannerController::class.':update')->setName('admin.bannerUpdate');//轮播图更新页面
        $this->post('', BannerController::class.':store')->setName('admin.bannerStore');//轮播图新增
        $this->put('', BannerController::class.':edit')->setName('admin.bannerEdit');//轮播图更新
        $this->delete('/{id}', BannerController::class.':delete')->setName('admin.bannerDelete');//轮播图删除
        $this->get('/status/{id}', BannerController::class.':status')->setName('admin.bannerStatus');//轮播图修改状态

    });
    $this->group('/news', function() {
        $this->get('', NewsController::class.':index')->setName('admin.newsTable')->add(new \App\Common\Middleware\PaginationMiddleware($this->getContainer()));//轮播图列表页面
        $this->get('/new', NewsController::class.':create')->setName('admin.newsCreate');//轮播图添加页面
        $this->get('/edit/{id}', NewsController::class.':update')->setName('admin.newsUpdate');//轮播图更新页面
        $this->post('', NewsController::class.':store')->setName('admin.newsStore');//轮播图新增
        $this->put('', NewsController::class.':edit')->setName('admin.newsEdit');//轮播图更新
        $this->delete('/{id}', NewsController::class.':delete')->setName('admin.newsDelete');//轮播图删除
        $this->get('/status/{id}', NewsController::class.':status')->setName('admin.newsStatus');//轮播图修改状态

    });
    $this->group('/message', function() {
        $this->get('', MessageController::class.':index')->setName('admin.MessageTable')->add(new \App\Common\Middleware\PaginationMiddleware($this->getContainer()));//轮播图列表页面
    });
    $this->group('/job', function() {
        $this->get('', JobController::class.':index')->setName('admin.jobTable')->add(new \App\Common\Middleware\PaginationMiddleware($this->getContainer()));//招聘职位列表页面
        $this->get('/new', JobController::class.':create')->setName('admin.jobCreate');//招聘职位添加页面
        $this->get('/edit/{id}', JobController::class.':update')->setName('admin.jobUpdate');//招聘职位更新页面
        $this->post('', JobController::class.':store')->setName('admin.jobStore');//招聘职位新增
        $this->put('', JobController::class.':edit')->setName('admin.jobEdit');//招聘职位更新
        $this->delete('/{id}', JobController::class.':delete')->setName('admin.jobDelete');//招聘职位删除
        $this->get('/status/{id}', JobController::class.':status')->setName('admin.jobStatus');//招聘职位修改状态

    });
    $this->group('/users', function() {
        $this->get('', UserController::class.':userList')->setName('admin.userList');//个人信息页面
        $this->get('/new', UserController::class.':addUser')->setName('admin.userCreate');//添加新的用户
        $this->post('', UserController::class.':store')->setName('admin.userStore');//添加新的用户
        $this->get('/status/{id}', UserController::class.':status')->setName('admin.userStatus');//修改用户状态
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

