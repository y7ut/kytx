<?php

use App\Controller\Admin\BoardController;

$app->group('/admin', function() {
    $this->get('', BoardController::class.':home')->setName('ADMIN_BOARD');//获取节点列表
    $this->get('/login', BoardController::class.':login')->setName('ADMIN_LOGIN');//获取节点列表
});
