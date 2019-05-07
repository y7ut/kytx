<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/7
 * Time: 10:56
 */
namespace App\Controller\Admin;

use App\Controller\ViewTrait;
use Slim\Http\Request;
use Slim\Http\Response;

final class BoardController{
    use ViewTrait;
    /**
     * 构造函数
     * @param null $container
     * @throws \Exception
     */
    public function __construct($container = null) {
        $this->setContainer($container);
    }

    /**
     * @param Request $request
     * @param $response $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function home(Request $request, Response $response){
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/board.html', [
            '11'
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function login(Request $request, Response $response){
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/login.html', []);
    }
}
