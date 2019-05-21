<?php

use App\Common\Middleware\AuthMiddleware;
use App\Controller\Admin\AuthController;
use App\Controller\admin\BannerController;
use App\Controller\Admin\BoardController;
use App\Controller\admin\CategoryController;
use App\Controller\admin\ChannelController;
use App\Controller\admin\DetailController;
use App\Controller\admin\JobController;
use App\Controller\admin\MessageController;
use App\Controller\admin\NewsController;
use App\Controller\admin\ProductController;
use App\Controller\admin\SizeController;
use App\Controller\admin\SkillController;
use App\Controller\admin\TypesController;
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
        $this->get('', NewsController::class.':index')->setName('admin.newsTable')->add(new \App\Common\Middleware\PaginationMiddleware($this->getContainer()));//新闻资讯列表页面
        $this->get('/new', NewsController::class.':create')->setName('admin.newsCreate');//新闻资讯添加页面
        $this->get('/edit/{id}', NewsController::class.':update')->setName('admin.newsUpdate');//新闻资讯更新页面
        $this->post('', NewsController::class.':store')->setName('admin.newsStore');//新闻资讯新增
        $this->put('', NewsController::class.':edit')->setName('admin.newsEdit');//新闻资讯更新
        $this->delete('/{id}', NewsController::class.':delete')->setName('admin.newsDelete');//新闻资讯删除
        $this->get('/status/{id}', NewsController::class.':status')->setName('admin.newsStatus');//新闻资讯修改状态

    });
    $this->group('/message', function() {
        $this->get('', MessageController::class.':index')->setName('admin.messageTable')->add(new \App\Common\Middleware\PaginationMiddleware($this->getContainer()));//客户消息列表页面
        $this->get('/{id}', MessageController::class.':show')->setName('admin.messageInfo');//客户消息查看
        $this->get('/status/{id}', MessageController::class.':status')->setName('admin.messageStatus');//客户消息修改状态
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
    $this->group('/detail', function() {
        $this->get('/intro', DetailController::class.':intro')->setName('admin.detailIntro');;//公司简介
        $this->put('/intro', DetailController::class.':intro')->setName('admin.detailIntroEdit');;//修改公司简介
        $this->get('/wish', DetailController::class.':wish')->setName('admin.detailWish');;//公司愿景
        $this->put('/wish', DetailController::class.':wish')->setName('admin.detailWishEdit');;//修改公司愿景
        $this->get('/contact', DetailController::class.':contact')->setName('admin.detailContact');;//联系
        $this->put('/contact', DetailController::class.':contact')->setName('admin.detailContactEdit');;//联系我们修改
        $this->get('/timeline', DetailController::class.':timeline')->setName('admin.detailTimeline');;//时间轴
        $this->get('/timeline/new', DetailController::class.':createTimeline')->setName('admin.detailTimelinePage');;//新增时间轴页面
        $this->post('/timeline', DetailController::class.':newTimeline')->setName('admin.detailTimelineCreate');;//新增时间轴页面
        $this->delete('/timeline/{id}', DetailController::class.':delTimeline')->setName('admin.detailTimelineDelete');;//删除时间轴
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
    $this->group('/categories', function() {
        $this->get('', CategoryController::class.':index')->setName('admin.categoryTable');//大分类管理列表
        $this->get('/new', CategoryController::class.':create')->setName('admin.categoryCreatePage');//新建
        $this->post('', CategoryController::class.':store')->setName('admin.categoryCreate');//新增
        $this->delete('/{id}', CategoryController::class.':delete')->setName('admin.categoryDelete');//删除
        $this->group('/{id}/product', function() {
            $this->get('', ProductController::class.':index')->setName('admin.ProductTable');//技术类型管理列表
        });
    });
    $this->group('/skill', function() {
        $this->get('', SkillController::class.':index')->setName('admin.skillTable');//技术类型管理列表
        $this->get('/new', SkillController::class.':create')->setName('admin.skillCreatePage');//新建
        $this->post('', SkillController::class.':store')->setName('admin.skillCreate');//新增
        $this->delete('/{id}', SkillController::class.':delete')->setName('admin.skillDelete');//删除
    });
    $this->group('/type', function() {
        $this->get('', TypesController::class.':index')->setName('admin.typeTable');//产品类型管理列表
        $this->get('/new', TypesController::class.':create')->setName('admin.typeCreatePage');//新建
        $this->post('', TypesController::class.':store')->setName('admin.typeCreate');//新增
        $this->delete('/{id}', TypesController::class.':delete')->setName('admin.typeDelete');//删除
    });
    $this->group('/channel', function() {
        $this->get('', ChannelController::class.':index')->setName('admin.channelTable');//产品频段管理列表
        $this->get('/new', ChannelController::class.':create')->setName('admin.channelCreatePage');//新建
        $this->post('', ChannelController::class.':store')->setName('admin.channelCreate');//新增
        $this->delete('/{id}', ChannelController::class.':delete')->setName('admin.channelDelete');//删除
    });
    $this->group('/size', function() {
        $this->get('', SizeController::class.':index')->setName('admin.sizeTable');//产品型号管理列表
        $this->get('/new', SizeController::class.':create')->setName('admin.sizeCreatePage');//新建
        $this->post('', SizeController::class.':store')->setName('admin.sizeCreate');//新增
        $this->delete('/{id}', SizeController::class.':delete')->setName('admin.sizeDelete');//删除
    });
})->add(new AuthMiddleware($app->getContainer()));

$app->group('/authentication', function() {
    $this->post('/auth', AuthController::class.':auth')->setName('admin.login');//登录请求
    $this->get('/logout', AuthController::class.':logout')->setName('admin.logout');//退出登录
    $this->get('/login', AuthController::class.':login')->setName('admin.loginPage');//登录页面
});

