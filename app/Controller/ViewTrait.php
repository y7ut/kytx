<?php
namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

/**
 * View 的Trait
 * @package App\Controller
 * @author Xie yuxuan
 */

trait ViewTrait{
    use containerTrait;

    /**
     * 向某一前端模板返回text/html的响应，并绑定变量
     *
     * @param Response $response
     * @param Request $request
     * @param string $viewPath
     * @param array|null $viewData
     * @return Response
     * @throws \Exception
     */
    protected function compact(Request $request, Response $response, string $viewPath, ?array $viewData){

        /** @var Response $view */
        return $this->fetch('view')->render($response, $viewPath, $viewData);
    }
}
