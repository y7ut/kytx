<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/21
 * Time: 11:11
 */

namespace App\Controller\admin;

use App\Controller\ViewTrait;
use App\Model\Category;
use App\Model\Channel;
use App\Model\Scope\CategoryScope;
use App\Model\Scope\ChannelScope;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

final class ChannelController
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
     * @param Request  $request
     * @param Response $response
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(Request $request, Response $response)
    {
        $father = $request->getQueryParam('father') ?? false;
        if ($father) {
            $fatherChannels = Channel::find($father)->allChildrenChannels()->get();
            $show = Channel::find($father);
        } else {
            $fatherChannels = Channel::all();
            $show = false;
        }

        return $this->compact($request, $response, 'Admin/product/channel/table.html', [
            'channels' => $fatherChannels,
            'show' => $show,
        ]);
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create(Request $request, Response $response)
    {
        $categories = Category::withoutGlobalScope(CategoryScope::class)->where('father_id', '!=', null)->get();
        $fatherChannels = Channel::all();

        return $this->compact($request, $response, 'Admin/product/channel/new.html', [
            'channels' => $fatherChannels,
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function store(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'name' => V::length(1, 12),
            'label' => V::length(1, 24),
            'father' => V::intVal(),
            'category_id' => V::intVal(),
        ]);

        $label = $request->getParam('label');
        $name = $request->getParam('name');
        $father = $request->getParam('father');
        $category_id = $request->getParam('category_id');

        if ('0' === $father && '0' === $category_id) {
            $this->validator->addError('category_id', '顶级的频段类型必须选择所属的产品类型');
        }

        if (!$this->validator->isValid()) {
            return $this->create($request, $response);
        }

        $channel = new Channel();

        $channel->name = $name;
        $channel->label = $label;
        $channel->category_id = $category_id;

        if (!empty($father)) {
            $channel->father_id = $father;
            $channel->category_id = Channel::find($father)->category_id;
        }

        $url = $this->router->pathFor('admin.channelTable');

        if ($channel->save()) {
            $this->flash->addMessage('success', '添加成功');
        } else {
            $this->flash->addMessage('error', '修改失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param mixed    $arg
     *
     * @return Response
     */
    public function delete(Request $request, Response $response, $arg)
    {
        $category = Channel::withoutGlobalScope(ChannelScope::class)->find($arg['id']);

        $url = $this->router->pathFor('admin.channelTable');
        if ($category->delete()) {
            $this->flash->addMessage('success', '操作成功');
        } else {
            $this->flash->addMessage('danger', '操作失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }
}
