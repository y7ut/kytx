<?php

use App\Controller\Admin\BoardController;

$app->get('/images', BoardController::class.':image')->setName('IMAGE');
