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
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

final class CategoryController
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
            $father = Category::find($father);

            if (null === $father) {
                throw new \Slim\Exception\NotFoundException($request, $response);
            }

            $fatherCategories = $father->allChildrenCategories()->get();
        } else {
            $fatherCategories = Category::all();
            $father = false;
        }

        return $this->compact($request, $response, 'Admin/product/category/table.html', [
            'categories' => $fatherCategories,
            'show' => $father,
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
        $fatherCategories = Category::all();

        return $this->compact($request, $response, 'Admin/product/category/new.html', [
            'categories' => $fatherCategories,
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
        ]);

        $label = $request->getParam('label');
        $name = $request->getParam('name');
        $father = $request->getParam('father');

        if (!$this->validator->isValid()) {
            return $this->create($request, $response);
        }

        $category = new Category();

        $category->name = $name;
        $category->label = $label;

        if (!empty($father)) {
            $category->father_id = $father;
        }

        $url = $this->router->pathFor('admin.categoryTable');

        if ($category->save()) {
            $this->flash->addMessage('success', '添加成功');
        } else {
            $this->flash->addMessage('error', '修改失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param $arg
     *
     * @throws \Slim\Exception\NotFoundException
     *
     * @return Response
     */
    public function delete(Request $request, Response $response, $arg)
    {
        $category = Category::withoutGlobalScope(CategoryScope::class)->find($arg['id']);

        if (null === $category) {
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        $url = $this->router->pathFor('admin.categoryTable');
        if ($category->delete()) {
            $this->flash->addMessage('success', '操作成功');
        } else {
            $this->flash->addMessage('danger', '操作失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }
}
