<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/7
 * Time: 10:56
 */

namespace App\Controller\Admin;

use App\Controller\ViewTrait;
use App\Model\Category;
use App\Model\Scope\CategoryScope;
use Slim\Http\Request;
use Slim\Http\Response;

final class BoardController
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
    public function home(Request $request, Response $response)
    {
        //向模板返回内容
        $category = Category::withoutGlobalScope(CategoryScope::class)->where('father_id', '!=', null)->get();

        return $this->compact($request, $response, 'Admin/board.html', [
            'category' => $category,
            'intro' => $this->db->table('details')->get()->first()['company_intro'],
        ]);
    }
}
