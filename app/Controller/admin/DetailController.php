<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/17
 * Time: 15:13
 */

namespace App\Controller\admin;

use App\Controller\ImageTrait;
use App\Controller\ViewTrait;
use App\Model\Timeline;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;

final class DetailController
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

    public function intro(Request $request, Response $response)
    {
        if ($request->isPut()) {
            $oldIntro = $this->db->table('details')->get()->first()['company_intro'];
            $url = $this->router->pathFor('admin.detailIntro');

            $this->validator->validate($request, [
                'intro' => V::length(1, 250),
            ]);

            $intro = $request->getParam('intro');

            if ($intro == $oldIntro) {
                $this->flash->addMessage('success', '修改成功');

                return $response->withStatus(302)->withHeader('Location', $url);
            }
            if (!$this->validator->isValid()) {
                return $this->compact($request, $response, 'Admin/detail/intro.html', [
                    'intro' => $intro,
                ]);
            }
            if ($this->db->table('details')->where('id', 1)->update(['company_intro' => $intro])) {
                $this->flash->addMessage('success', '修改成功');
            } else {
                $this->flash->addMessage('error', '修改失败');
            }

            return $response->withStatus(302)->withHeader('Location', $url);
        }

        return $this->compact($request, $response, 'Admin/detail/intro.html', [
            'intro' => $this->db->table('details')->get()->first()['company_intro'],
        ]);
    }

    public function wish(Request $request, Response $response)
    {
        if ($request->isPut()) {
            $oldWish = $this->db->table('details')->get()->first()['company_wish'];
            $url = $this->router->pathFor('admin.detailWish');

            $this->validator->validate($request, [
                'wish' => V::length(1, 250),
            ]);

            $wish = $request->getParam('wish');

            if ($wish == $oldWish) {
                $this->flash->addMessage('success', '修改成功');

                return $response->withStatus(302)->withHeader('Location', $url);
            }
            if (!$this->validator->isValid()) {
                return $this->compact($request, $response, 'Admin/detail/wish.html', [
                    'wish' => $wish,
                ]);
            }
            if ($this->db->table('details')->where('id', 1)->update(['company_wish' => $wish])) {
                $this->flash->addMessage('success', '修改成功');
            } else {
                $this->flash->addMessage('error', '修改失败');
            }

            return $response->withStatus(302)->withHeader('Location', $url);
        }

        return $this->compact($request, $response, 'Admin/detail/wish.html', [
            'wish' => $this->db->table('details')->get()->first()['company_wish'],
        ]);
    }

    public function contact(Request $request, Response $response)
    {
        if ($request->isPut()) {
            $oldContact = $this->db->table('details')->get()->first()['contact_us'];
            $url = $this->router->pathFor('admin.detailContact');

            $this->validator->validate($request, [
                'contact' => V::length(1, 250),
            ]);

            $contact = $request->getParam('contact');

            if ($contact == $oldContact) {
                $this->flash->addMessage('success', '修改成功');

                return $response->withStatus(302)->withHeader('Location', $url);
            }
            if (!$this->validator->isValid()) {
                return $this->compact($request, $response, 'Admin/detail/contact.html', [
                    'contact' => $contact,
                ]);
            }
            if ($this->db->table('details')->where('id', 1)->update(['contact_us' => $contact])) {
                $this->flash->addMessage('success', '修改成功');
            } else {
                $this->flash->addMessage('error', '修改失败');
            }

            return $response->withStatus(302)->withHeader('Location', $url);
        }

        return $this->compact($request, $response, 'Admin/detail/contact.html', [
            'contact' => $this->db->table('details')->get()->first()['contact_us'],
        ]);
    }

    public function timeline(Request $request, Response $response)
    {
        $timeLines = Timeline::all()->sortBy('time');

        return $this->compact($request, $response, 'Admin/detail/timeline.html', [
            'timelines' => $timeLines,
        ]);
    }

    public function createTimeline(Request $request, Response $response)
    {
        return $this->compact($request, $response, 'Admin/detail/newTimeline.html');
    }

    public function newTimeline(Request $request, Response $response)
    {
        $this->validator->validate($request, [
            'title' => V::length(1, 24),
        ]);

        $title = $request->getParam('title');
        $time = $request->getParam('time');

        if (!$this->validator->isValid()) {
            return $this->createTimeline($request, $response);
        }

        $timeline = new Timeline();
        $timeline->title = $title;
        $timeline->time = $time;

        $files = $request->getUploadedFiles();
        if (!empty($files['image']->file)) {
            if (!V::image()->validate($files['image']->file)) {
                // 若不是图片返回错误提示
                $this->validator->addError('image', '上传内容只能为图片');

                return $this->createTimeline($request, $response);
            }
            $fileName = $this->uploadImage($files['image'], 60, [300, 300]);
            $timeline->image = $fileName;
        } else {
            $this->validator->createTimeline('image', '请上传轮播图');

            return $this->create($request, $response);
        }

        $url = $this->router->pathFor('admin.detailTimeline');

        if ($timeline->save()) {
            $this->flash->addMessage('success', '添加成功');
        } else {
            $this->flash->addMessage('error', '修改失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }

    public function delTimeline(Request $request, Response $response, $arg)
    {
        $timeline = Timeline::find($arg['id']);

        $imageSrc = $timeline->img;

        $url = $this->router->pathFor('admin.detailTimeline');
        if ($timeline->delete()) {
            $this->delImage($imageSrc);
            $this->flash->addMessage('success', '操作成功');
        } else {
            $this->flash->addMessage('danger', '操作失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }
}
