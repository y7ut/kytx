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

class CarefullyDeleteMiddleware
{
    use containerTrait;

    /** @var ContainerInterface 依赖注入容器引用 */
    private $containers;

    /**
     * 构造函数
     *
     * @param $containers
     */
    public function __construct($containers)
    {
        $this->container = $containers;
        if (!$containers->has('auth')) {
            throw new \RuntimeException('There is no auth setting in container');
        }
        if (!$containers->has('flash')) {
            throw new \RuntimeException('There is no flash setting in container');
        }
    }

    public function __invoke($request, $response, $next)
    {
        if ($request->isDelete() && ENV_DEVELOPMENT) {
            $url = $request->getUri()->getPath();
            $url = substr($url,0,-(strlen($url)-strrpos($url, '/')));
            $this->flash->addMessage('success', '哎哎哎别删!');
            return $response->withStatus(302)->withHeader('Location', $url);
        }

        return $next($request, $response);
    }
}
