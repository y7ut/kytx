<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/23
 * Time: 18:22
 */

use App\Common\Middleware\CORSMiddleware;
use App\Controller\Api\ApiController;

$app->group('/api', function() {
    $this->get('/category', ApiController::class.':getCategories')->setName('api.category');//获取A侧的数据
    $this->get('/category/{id}/config', ApiController::class.':getConfig')->setName('api.config');//获取A侧的数据
})->add(CORSMiddleware::class);
