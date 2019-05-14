<?php

namespace App\Controller;

use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * View 的Trait
 *
 * @author Xie yuxuan
 */
trait ViewTrait
{
    use containerTrait;

    /**
     * 向某一前端模板返回text/html的响应，并绑定变量
     *
     * @param Response   $response
     * @param Request    $request
     * @param string     $viewPath
     * @param array|null $viewData
     *
     * @throws \Exception
     *
     * @return Response
     */
    protected function compact(Request $request, Response $response, string $viewPath, ?array $viewData = [])
    {
        /* @var Response $view */
        return $this->fetch('view')->render($response, $viewPath, $viewData);
    }

    /**
     * @param string $message
     */
    protected function success(string $message)
    {
        $this->flash->addMessage('success', $message);
    }
}
