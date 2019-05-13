<?php
/**
 * 验证模块
 * User: YiChu
 * Date: 2019/5/13
 * Time: 17:15
 */

namespace App\Controller\Admin;


use App\Controller\ViewTrait;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

final class AuthController
{
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
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function login(Request $request, Response $response){
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/login.html', []);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function auth(Request $request, Response $response){
        $this->validator->validate($request, [
            'email' => V::notBlank()->email(),
            'password' => V::length(5, 25),
        ]);
        if ($this->validator->isValid()) {
            $user = $this->auth->login($request);
            $url = $this->router->pathFor('admin.board');
            // Set flash message for next request
            $this->flash->addMessage('success', sprintf('欢迎您%s，登录开元通信后台管理平台', $user->email));
            return $response->withStatus(302)->withHeader('Location', $url);
        }else{
            //有错误信息
            $this->compact($request, $response, 'Admin/login.html', []);
        }
    }

    /**
     * 注销登录
     *
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function logout(Request $request, Response $response){
        $this->auth->logout();
        $url = $this->router->pathFor('admin.loginPage');
        $this->flash->addMessage('danger', '已退出登录');
        return $response->withStatus(302)->withHeader('Location', $url);
    }
}
