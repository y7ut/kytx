<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/16
 * Time: 17:32
 */

namespace App\Controller\admin;

use App\Controller\ViewTrait;
use App\Model\Message;
use Slim\Http\Request;
use Slim\Http\Response;

class MessageController
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
     * @param Request $request
     * @param $response $response
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(Request $request, Response $response)
    {
        $messages = Message::paginate(10);
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/message/table.html', [
            'messages' => $messages,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $arg
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Slim\Exception\NotFoundException
     */
    public function show(Request $request, Response $response, array $arg)
    {
        $id = $arg['id'];
        $message = Message::find($id);

        if(is_null($message)){
            throw new \Slim\Exception\NotFoundException($request, $response);
        }


        if (0 === $message->status) {
            $message->status = 1;
        }

        $message->save();

        return $this->compact($request, $response, 'Admin/message/info.html', [
            'message' => $message,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $arg
     * @return Response
     * @throws \Slim\Exception\NotFoundException
     */
    public function status(Request $request, Response $response, array $arg)
    {
        $id = $arg['id'];
        $message = Message::find($id);

        if(is_null($message)){
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        if (0 === $message->status) {
            $message->status = 1;
        } else {
            $message->status = 0;
        }

        $url = $this->router->pathFor('admin.messageTable');

        if ($message->save()) {
            $this->flash->addMessage('success', '操作成功');
        } else {
            $this->flash->addMessage('error', '操作失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }
}
