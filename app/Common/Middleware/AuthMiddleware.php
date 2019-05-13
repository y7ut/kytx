<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/13
 * Time: 16:37
 */

namespace App\Common\Middleware;


use App\Controller\containerTrait;
use Psr\Container\ContainerInterface;

class AuthMiddleware
{
    use containerTrait;

    /**@var ContainerInterface 依赖注入容器引用 */
    private $containers;

    /**
     * 构造函数
     * @param $containers
     */
    public function __construct($containers) {
        $this->container = $containers;
        if (!$containers->has('auth')){
            throw new \RuntimeException('There is no auth setting in container');
        }
    }

    public function __invoke($request, $response, $next)
    {
        if(!$this->auth->user()){
            $url = $this->router->pathFor('admin.loginPage');
            $this->flash->addMessage('danger', '请重新登录');
            return $response->withStatus(302)->withHeader('Location', $url);
        }
        return $next($request, $response);
    }

}
