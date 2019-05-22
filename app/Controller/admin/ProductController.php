<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/21
 * Time: 11:11
 */

namespace App\Controller\admin;

use App\Controller\ImageTrait;
use App\Controller\ViewTrait;
use App\Model\Category;
use App\Model\Product;
use App\Model\Scope\CategoryScope;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

final class ProductController
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
     * @param mixed    $arg
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index(Request $request, Response $response, $arg)
    {
        $id = $arg['id'];
        $category = Category::withoutGlobalScope(CategoryScope::class)->find($id);

        if(is_null($category)){
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        $products = Product::all();

        return $this->compact($request, $response, 'Admin/product/table.html', [
            'products' => $products,
            'category' => $category,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @param array $arg
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Slim\Exception\NotFoundException
     */
    public function create(Request $request, Response $response, array $arg)
    {
        $id = $arg['id'];
        $category = Category::withoutGlobalScope(CategoryScope::class)->find($id);
        if(is_null($category)){
            throw new \Slim\Exception\NotFoundException($request, $response);
        }

        return $this->compact($request, $response, 'Admin/product/new.html', [
            'category' => $category,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @param array $arg
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Slim\Exception\NotFoundException
     */
    public function store(Request $request, Response $response,array $arg)
    {
        $id = $arg['id'];
        $category = Category::withoutGlobalScope(CategoryScope::class)->find($id);
        if(is_null($category)){
            throw new \Slim\Exception\NotFoundException($request, $response);
        }
        $configs = $category->configs;
        //生成验证规则
        $validatorArray = $configs->map(function ($item){
            return [$item->config_type=> V::intVal()];
        })->collapse()->toArray();
        $validatorArray['name'] = V::notBlank();
        $validatorArray['content'] = V::notBlank();
        $validatorArray['intro'] = V::notBlank()->length(1,150);

        $this->validator->validate($request,$validatorArray);

        if (!$this->validator->isValid()) {
            return $this->create($request, $response, $arg);
        }

        $files = $request->getUploadedFiles();

        if (empty($files['image']->file)) {
            $this->validator->addError('image', '请上传封面缩略图');

            return $this->create($request, $response, $arg);
        }
        if (!V::image()->validate($files['image']->file)) {
            // 若不是图片返回错误提示
            $this->validator->addError('image', '上传内容只能为图片');

            return $this->create($request, $response, $arg);
        }

        $fileName = $this->uploadImage($files['image']);

        $product = new Product();

        $product->name = $request->getParam('name');
        $product->intro = $request->getParam('intro');
        $product->content = $request->getParam('content');
        $product->category_id = $id;

        foreach ($configs as $item){
            $label = $item->config_type.'_id';
            $product->$label = $request->getParam($item->config_type);
        }

        $product->image = $fileName;

        $url = $this->router->pathFor('admin.productTable',['id'=>$id]);


        if ($product->save()) {
            $this->flash->addMessage('success', '添加成功');
        } else {
            $this->flash->addMessage('error', '添加失败');
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
    }
}
