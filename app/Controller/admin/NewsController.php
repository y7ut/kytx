<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/15
 * Time: 17:44
 */

namespace App\Controller\admin;

use App\Controller\ImageTrait;
use App\Controller\ViewTrait;
use App\Model\News;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

final class NewsController
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
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function index(Request $request, Response $response){

        $news = News::paginate(3);
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/news/table.html', [
            'news' => $news,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function create(Request $request, Response $response){
        return $this->compact($request, $response, 'Admin/news/new.html');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function store(Request $request, Response $response){
        $this->validator->validate($request, [
            'title' => V::notEmpty()->length(1, 24),
            'content' => V::notEmpty()->stringType(),
        ]);

        $title = $request->getParam('title');
        if (!$this->validator->isValid()) {
            return $this->create($request, $response);
        }
        $files = $request->getUploadedFiles();

        $news = New News();
        $news->title = $title;
        $news->content = $request->getParam('content');
        $news->type = $request->getParam('type');
        $news->user_id = $this->auth->id();
        $news->hot = 0;

        if (empty($files['img']->file)) {
            $this->validator->addError('img', '请上传封面缩略图');
            return $this->create($request, $response);
        }
        if (!V::image()->validate($files['img']->file)) {
            // 若不是图片返回错误提示
            $this->validator->addError('img', '上传内容只能为图片');

            return $this->create($request, $response);
        }
        $fileName = $this->uploadImage($files['img']);

        $news->img = $fileName;

        $url = $this->router->pathFor('admin.newsTable');

        if ($news->save()) {
            $this->flash->addMessage('success', '添加成功');
        } else {
            $this->flash->addMessage('error', '添加失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }
    public function update(Request $request, Response $response, array $arg){
        $id = $arg['id'];
        $news = News::find($id);

        return $this->compact($request, $response, 'Admin/news/edit.html', [
            'news' => $news,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function edit(Request $request, Response $response){
        $this->validator->validate($request, [
            'title' => V::notEmpty()->length(1, 24),
            'content' => V::notEmpty()->stringType(),
        ]);
        $id = $request->getParam('id');
        $title = $request->getParam('title');
        if (!$this->validator->isValid()) {
            $arg['id'] = $id;
            return $this->update($request, $response, $arg);
        }
        $files = $request->getUploadedFiles();
        $news = News::find($id);
        $news->title = $title;
        $news->content = $request->getParam('content');
        $news->type = $request->getParam('type');
        $delName = $news->img;
        if (!empty($files['img']->file)) {
            if (!V::image()->validate($files['img']->file)) {
                // 若不是图片返回错误提示
                $this->validator->addError('img', '上传内容只能为图片');
                $arg['id'] = $id;
                return $this->update($request, $response, $arg);
            }
            $fileName = $this->uploadImage($files['img']);
            $news->img = $fileName;
        }

        $url = $this->router->pathFor('admin.newsTable');

        if ($news->save()) {
            $this->delImage($delName);
            $this->flash->addMessage('success', '修改成功');
        } else {
            $this->flash->addMessage('error', '修改失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $arg
     * @return Response
     */
    public function delete(Request $request, Response $response, $arg){
        $banner = News::find($arg['id']);
        $imageSrc = $banner->img;
        $url = $this->router->pathFor('admin.bannerTable');
        if ($banner->delete()) {
            $this->delImage($imageSrc);
            $this->flash->addMessage('success', '操作成功');
        } else {
            $this->flash->addMessage('danger', '操作失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    /**
     * 修改文章的热度状态
     *
     * @param Request $request
     * @param Response $response
     * @param array $arg
     * @return Response
     */
    public function status(Request $request, Response $response, array $arg){
        $id = $arg['id'];
        $url = $this->router->pathFor('admin.newsTable');

        $user = News::find($id);

        if (0 === $user->hot) {
            $hotCount = News::where('hot',1)->count();
            if(2==$hotCount){
                $this->flash->addMessage('error', '最多置顶4个新闻或资讯');
                return $response->withStatus(302)->withHeader('Location', $url);
            }
            $user->hot = 1;
        } else {
            $user->hot = 0;
        }


        if ($user->save()) {
            $this->flash->addMessage('success', '操作成功');
        } else {
            $this->flash->addMessage('error', '操作失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }
}
