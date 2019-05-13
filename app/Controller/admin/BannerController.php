<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/13
 * Time: 18:31
 */

namespace App\Controller\admin;

use App\Controller\ViewTrait;
use Slim\Http\Request;
use Slim\Http\Response;

class BannerController
{
    use ViewTrait;

    /**
     * 构造函数
     *
     * @param null $container
     *
     * @throws \Exception
     */
    public function __construct($container = null)
    {
        $this->setContainer($container);
    }

    public function index(Request $request, Response $response)
    {
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/banner/table.html');
    }
}
