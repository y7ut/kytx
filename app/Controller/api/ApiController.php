<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/23
 * Time: 18:09
 */

namespace App\Controller\Api;

use App\Controller\containerTrait;
use App\Model\Category;
use App\Model\Scope\CategoryScope;
use Slim\Http\Request;
use Slim\Http\Response;

class ApiController
{
    use containerTrait;

    /**
     * @param Request $request
     * @param $response $response
     *
     * @return Response
     */
    public function getCategories(Request $request, Response $response)
    {
        $categories = Category::all()->map(function ($item) {
            return [
                'name' => $item->name,
                'son' => $item->allChildrenCategories->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                    ];
                })->toArray(),
            ];
        })->toArray();
        $result = [
            'status' => 0,
        ];
        $result['category'] = $categories;

        return $response->withStatus(200)->withJson($result);
    }

    /**
     * @param Request $request
     * @param $response $response
     * @param mixed $arg
     *
     * @return Response
     */
    public function getConfig(Request $request, Response $response, $arg)
    {
        $id = $arg['id'];
        $category = Category::withoutGlobalScope(CategoryScope::class)->find($id);
        $config = $category->configs->map(function ($item) use ($category) {
            $configItem = [
                'title' => $item->config_type,
                'label' => $item->label,
                'select' => boolval($item->select),
                'extend' => [],
            ];
            $fuc = $item->config_type.'s';
            $configItem['data'] = $category->$fuc->pluck('id', 'name')->toArray();
            if ('channel' == $item->config_type) {
                $configItem['extend'] = $category->channelFathers->pluck('name')->toArray();
                $configItem['data'] = $category->$fuc->filter(function ($item) {
                    return null != $item->father_id;
                })->pluck('id', 'name')->toArray();
            }

            return $configItem;
        })->toArray();
        $result = [
            'status' => 0,
        ];
        $result['category'] = $config;

        return $response->withStatus(200)->withJson($result);
    }
}
