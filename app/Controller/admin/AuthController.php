<?php
/**
 * 验证模块
 * User: YiChu
 * Date: 2019/5/13
 * Time: 17:15
 */

namespace App\Controller\Admin;

use App\Controller\ImageTrait;
use App\Controller\ViewTrait;
use App\Model\AdminUser;
use Carbon\Carbon;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

final class AuthController
{
    use ViewTrait;
    use ImageTrait;

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

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function login(Request $request, Response $response)
    {
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/login.html', []);
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function auth(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'email' => V::notBlank()->email(),
            'password' => V::length(5, 25),
        ]);
        if ($this->validator->isValid()) {
            $user = $this->auth->login($request);
            $url = $this->router->pathFor('admin.board');
            // Set flash message for next request
            $this->flash->addMessage('success', sprintf('你好 %s，欢迎回来', $user->email));

            return $response->withStatus(302)->withHeader('Location', $url);
        } else {
            //有错误信息
            $this->login($request, $response);
        }
    }

    /**
     * 注销登录
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function logout(Request $request, Response $response)
    {
        $this->auth->logout();
        $url = $this->router->pathFor('admin.loginPage');
        $this->flash->addMessage('danger', '已退出登录');

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @throws \Exception
     */
    public function info(Request $request, Response $response)
    {
        $this->compact($request, $response, 'Admin/auth/info.html', []);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function infoEdit(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'oldPassword' => V::length(5, 12),
            'password' => V::length(5, 12),
            'newPassword' =>  V::equals($request->getParam('password')),
        ]);
        if ($this->validator->isValid()) {


            if (md5($request->getParam('oldPassword')) == $this->auth->user()->password) {
                $user = AdminUser::find($this->auth->user()->id);
                $user->password = $request->getParam('password');
                $user->save();
                $this->auth->logout();
                $url = $this->router->pathFor('admin.loginPage');
                $this->flash->addMessage('danger', '请重新登录');

                return $response->withStatus(302)->withHeader('Location', $url);
            } else {
                $this->validator->addError('oldPassword', '请输入正确的密码');
                $this->info($request, $response);
            }



        } else {
            //有错误信息
            $this->info($request, $response);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function avatarEdit(Request $request, Response $response)
    {
        //获取上传图片对象
        $files = $request->getUploadedFiles();

        //验证为图片
        if (V::image()->validate($files['avatar']->file)) {
            $fileName = $this->imageResize($files['avatar'],30,[300,300]);
            $this->auth->user()->avatar = $fileName;
            $user = AdminUser::find($this->auth->user()->id);
            $this->delImage($user->avatar);
            $user->avatar =$fileName;
            $user->save();

            $url = $this->router->pathFor('admin.userEditPage');

            $this->flash->addMessage('success', '修改成功');
            return $response->withStatus(302)->withHeader('Location', $url);

        } else {
            //有错误信息
            $this->validator->addError('avatar', '上传内容只能为图片');
            $this->info($request, $response);
        }
    }
}
