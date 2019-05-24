<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/15
 * Time: 16:19
 */

namespace App\Controller\admin;

use App\Controller\ViewTrait;
use App\Model\AdminUser;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

final class UserController
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

    /**
     * 更换banner状态
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $arg
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function status(Request $request, Response $response, array $arg)
    {
        $id = $arg['id'];
        $url = $this->router->pathFor('admin.userList');

        if ($id == $this->auth->id()) {
            $this->flash->addMessage('error', '不可操作当前登录用户');

            return $response->withStatus(302)->withHeader('Location', $url);
        }

        $user = AdminUser::find($id);

        if (null === $user) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        if (0 === $user->status) {
            $user->status = 1;
        } else {
            $user->status = 0;
        }

        if ($user->save()) {
            $this->flash->addMessage('success', '操作成功');
        } else {
            $this->flash->addMessage('error', '操作失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    public function userList(Request $request, Response $response)
    {
        $user = AdminUser::paginate(6);
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/user/table.html', [
            'user' => $user,
        ]);
    }

    public function addUser(Request $request, Response $response)
    {
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/user/new.html');
    }

    public function store(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'name' => V::length(5, 12),
            'email' => V::email(),
        ]);
        if ($this->validator->isValid()) {
            $name = $request->getParam('name');
            $email = $request->getParam('email');

            if (null !== AdminUser::where('email', $email)->get()->first()) {
                $this->validator->addError('email', '该邮箱已被注册');

                return $this->addUser($request, $response);
            }
            $user = new AdminUser();
            $user->password = 'admin';
            $user->name = $name;
            $user->email = $email;
            $user->avatar = '/assets/img/avatar/avatar-1.jpeg.jpg';
            $user->status = 1;

            if ($user->save()) {
                $this->flash->addMessage('success', '添加成功');
            } else {
                $this->flash->addMessage('danger', '添加失败');
            }

            $url = $this->router->pathFor('admin.userList');

            return $response->withStatus(302)->withHeader('Location', $url);
        } else {
            //有错误信息
            return $this->addUser($request, $response);
        }
    }
}
