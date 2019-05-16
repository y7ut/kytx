<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/16
 * Time: 14:28
 */

namespace App\Controller\admin;

use App\Controller\ViewTrait;
use App\Model\Job;
use App\Model\News;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as V;


final class JobController
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
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function index(Request $request, Response $response){

        $jobs = Job::paginate(6);
        //向模板返回内容
        return $this->compact($request, $response, 'Admin/job/table.html', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function create(Request $request, Response $response){
        return $this->compact($request, $response, 'Admin/job/new.html');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Exception
     */
    public function store(Request $request, Response $response){
        $this->validator->validate($request, [
            'title' => V::notBlank()->length(1, 24),
            'address' => V::notBlank()->length(1, 24),
            'job_intro' => V::notBlank()->length(1, 150),
            'work_intro' => V::notBlank()->length(1, 150),
            'max_wage' => V::notBlank()->numeric(),
            'min_wage' => V::notBlank()->numeric(),
            'max_experience' => V::notBlank()->numeric(),
            'min_experience' => V::notBlank()->numeric(),
            'education' => V::notBlank()->stringType(),
        ]);

        if (!$this->validator->isValid()) {
            return $this->create($request, $response);
        }

        $job = new Job();

        $job->title = $request->getParam('title');
        $job->address = $request->getParam('address');
        $job->work_category = $request->getParam('work_category');
        $job->job_intro = $request->getParam('job_intro');
        $job->work_intro = $request->getParam('work_intro');
        $job->max_wage = $request->getParam('max_wage').'k';
        $job->min_wage = $request->getParam('min_wage').'k';
        $job->max_experience = $request->getParam('max_experience').'年';
        $job->min_experience = $request->getParam('min_experience').'年';
        $job->education = $request->getParam('education');
        $job->status = 1;

        $url = $this->router->pathFor('admin.jobTable');

        if ($job->save()) {
            $this->flash->addMessage('success', '添加成功');
        } else {
            $this->flash->addMessage('error', '添加失败');
        }

        return $response->withStatus(302)->withHeader('Location', $url);
    }
    public function update(Request $request, Response $response, array $arg){
        $id = $arg['id'];
        $job = Job::find($id);

        return $this->compact($request, $response, 'Admin/job/edit.html', [
            'job' => $job,
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface|Response
     */
    public function edit(Request $request, Response $response){
        $this->validator->validate($request, [
            'title' => V::notBlank()->length(1, 24),
            'address' => V::notBlank()->length(1, 24),
            'job_intro' => V::notBlank()->length(1, 150),
            'work_intro' => V::notBlank()->length(1, 150),
            'max_wage' => V::notBlank()->numeric(),
            'min_wage' => V::notBlank()->numeric(),
            'max_experience' => V::notBlank()->numeric(),
            'min_experience' => V::notBlank()->numeric(),
            'education' => V::notBlank()->stringType(),
        ]);
        $id =  $request->getParam('id');
        if (!$this->validator->isValid()) {
            $arg['id'] = $id;
            return $this->update($request, $response, $arg);
        }

        $job = Job::find($id);

        $job->title = $request->getParam('title');
        $job->address = $request->getParam('address');
        $job->work_category = $request->getParam('work_category');
        $job->job_intro = $request->getParam('job_intro');
        $job->work_intro = $request->getParam('work_intro');
        $job->max_wage = $request->getParam('max_wage').'k';
        $job->min_wage = $request->getParam('min_wage').'k';
        $job->max_experience = $request->getParam('max_experience').'年';
        $job->min_experience = $request->getParam('min_experience').'年';
        $job->education = $request->getParam('education');
        $job->status = 1;

        $url = $this->router->pathFor('admin.jobTable');

        if ($job->save()) {
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
        $banner = Job::find($arg['id']);

        $url = $this->router->pathFor('admin.jobTable');

        if ($banner->delete()) {
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
        $url = $this->router->pathFor('admin.jobTable');

        $user = Job::find($id);

        if (0 === $user->status) {
//            $Count = Job::where('hot',1)->count();
//            if(2==$Count){
//                $this->flash->addMessage('error', '最多置顶2个招聘');
//                return $response->withStatus(302)->withHeader('Location', $url);
//            }
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
}
