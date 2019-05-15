<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/15
 * Time: 15:09
 */

namespace App\Common\Middleware;

use App\Controller\containerTrait;
use Illuminate\Pagination\Paginator;
use Psr\Container\ContainerInterface;
use Twig\TwigTest;

class PaginationMiddleware
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
        if (!$containers->has('view')) {
            throw new \RuntimeException('There is no view setting in container');
        }
    }

    public function __invoke($request, $response, $next)
    {
        $current_page = $request->getParam('page');

        Paginator::currentPageResolver(function () use ($current_page) {
            return $current_page;
        });

        $view = $this->container->get('view');

        $view->getEnvironment()->addTest(new TwigTest('string', function ($value) {
            return is_string($value);
        }));

        Paginator::viewFactoryResolver(function () use ($view) {
            return new class($view) {
                private $view;
                private $template;
                private $data;

                public function __construct(\Slim\Views\Twig $view)
                {
                    $this->view = $view;
                }

                public function make(string $template, $data = null)
                {
                    $this->template = $template;
                    $this->data = $data;

                    return $this;
                }

                public function render()
                {
                    return $this->view->fetch($this->template, $this->data);
                }
            };
        });

        Paginator::currentPageResolver(function () use ($request) {
            return $request->getParam('page');
        });

        return $next($request, $response);
    }
}
