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
use App\Model\Scope\CategoryScope;
use App\Model\Size;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

final class SizeController
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
        $sizes = Size::all();

        return $this->compact($request, $response, 'Admin/product/size/table.html', [
            'sizes' => $sizes,
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

        return $this->compact($request, $response, 'Admin/product/size/new.html', [
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function store(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'name' => V::length(1, 72),
            'label' => V::length(1, 72),
            'category_id' => V::intVal(),
        ]);

        $label = $request->getParam('label');
        $name = $request->getParam('name');
        $category_id = $request->getParam('category_id');

        if (!$this->validator->isValid()) {
            return $this->create($request, $response);
        }

        $size = new Size();

        $size->name = $name;
        $size->label = $label;
        $size->category_id = $category_id;

        $url = $this->router->pathFor('admin.sizeTable');

        if ($size->save()) {
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
        $skill = Size::find($arg['id']);

        $url = $this->router->pathFor('admin.sizeTable');
        if ($skill->delete()) {
            $this->flash->addMessage('success', '操作成功');
        } else {
            $this->flash->addMessage('danger', '操作失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }
}
