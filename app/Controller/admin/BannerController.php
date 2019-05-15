<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/13
 * Time: 18:31
 */

namespace App\Controller\admin;

use App\Controller\ImageTrait;
use App\Controller\ViewTrait;
use App\Model\Banner;
use Illuminate\Database\QueryException;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

class BannerController
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
    public function index(Request $request, Response $response)
    {
        $banner = Banner::paginate(4);
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/banner/table.html', [
            'banner' => $banner,
        ]);
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
        $banner = Banner::find($id);

        if (0 === $banner->status) {
            $banner->status = 1;
        } else {
            $banner->status = 0;
        }
        $banner->save();
        $url = $this->router->pathFor('admin.bannerTable');
        try {
            $banner->save();
        } catch (QueryException $e) {
            $this->flash->addMessage('error', '修改失败:'.$e->getMessage());

            return $response->withStatus(302)->withHeader('Location', $url);
        }

        $this->flash->addMessage('success', '修改成功');

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param array    $arg
     *
     * @throws \Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function update(Request $request, Response $response, array $arg)
    {
        $id = $arg['id'];
        $banner = Banner::find($id);

        return $this->compact($request, $response, 'Admin/banner/edit.html', [
            'banner' => $banner,
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
        return $this->compact($request, $response, 'Admin/banner/new.html');
    }

    public function store(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'title' => V::length(1, 24),
            'url' => V::stringType(),
        ]);

        $title = $request->getParam('title');
        $url = $request->getParam('url');

        if (!$this->validator->isValid()) {
            return $this->create($request, $response);
        }

        $banner = new Banner();
        $banner->title = $title;
        $banner->url = $url;

        $files = $request->getUploadedFiles();
        if (!empty($files['banner']->file)) {
            if (!V::image()->validate($files['banner']->file)) {
                // 若不是图片返回错误提示
                $this->validator->addError('banner', '上传内容只能为图片');

                return $this->create($request, $response);
            }
            $fileName = $this->uploadImage($files['banner']);
            $banner->src = $fileName;
        } else {
            $this->validator->addError('banner', '请上传轮播图');

            return $this->create($request, $response);
        }

        $url = $this->router->pathFor('admin.bannerTable');

        if ($banner->save()) {
            $this->flash->addMessage('success', '修改成功');
        } else {
            $this->flash->addMessage('error', '修改失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    public function edit(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'title' => V::length(1, 24),
            'url' => V::stringType(),
        ]);

        $bannerId = $request->getParam('id');
        $title = $request->getParam('title');
        $url = $request->getParam('url');

        if (!$this->validator->isValid()) {
            $arg['id'] = $bannerId;

            return $this->update($request, $response, $arg);
        }

        $banner = Banner::find($bannerId);
        $banner->title = $title;
        $banner->url = $url;

        $files = $request->getUploadedFiles();
        if (!empty($files['banner']->file)) {
            if (!V::image()->validate($files['banner']->file)) {
                // 若不是图片返回错误提示
                $arg['id'] = $bannerId;
                $this->validator->addError('banner', '上传内容只能为图片');

                return $this->update($request, $response, $arg);
            }
            $fileName = $this->uploadImage($files['banner']);
            $banner->src = $fileName;
        }

        $url = $this->router->pathFor('admin.bannerUpdate', ['id' => $bannerId]);

        if ($banner->save()) {
            $this->delImage($fileName);
            $this->flash->addMessage('success', '修改成功');
        } else {
            $this->flash->addMessage('error', '修改失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    public function delete(Request $request, Response $response, array $arg)
    {
        $banner = Banner::find($arg['id']);
        $this->delImage($banner->src);
        $url = $this->router->pathFor('admin.bannerTable');
        if ($banner->delete()) {
            $this->flash->addMessage('success', '操作成功');
        } else {
            $this->flash->addMessage('danger', '操作失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }
}
