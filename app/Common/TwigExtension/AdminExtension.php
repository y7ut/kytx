<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/13
 * Time: 17:26
 */

namespace App\Common\TwigExtension;

use App\Common\Auth\AdminAuth;
use App\Model\Category;
use App\Model\Product;
use App\Model\Scope\CategoryScope;
use Awurth\SlimValidation\Validator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    protected $adminAuth;
    protected $validator;

    /**
     * Constructor.
     *
     * @param AdminAuth $adminAuth
     * @param Validator $validator
     */
    public function __construct(AdminAuth $adminAuth, Validator $validator)
    {
        $this->adminAuth = $adminAuth;
        $this->validator = $validator;
    }

    /**
     * Extension name.
     *
     * @return string
     */
    public function getName()
    {
        return 'auth_admin_user';
    }

    /**
     * Callback for twig.
     *
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('get_user', [$this, 'getUser']),
            new TwigFunction('get_page', [$this, 'getPage']),
            new TwigFunction('last_page', [$this, 'lastPage']),
            new TwigFunction('add_option', [$this, 'addOption']),
            new TwigFunction('to_number', [$this, 'toNumber']),
            new TwigFunction('product_aside', [$this, 'getProductAside']),
            new TwigFunction('select_item', [$this, 'getSelectItem']),
            new TwigFunction('table_config', [$this, 'tableConfig']),
        ];
    }

    /**
     * 获取用户的用户名（name）
     *
     * @param string $key
     *
     * @return string
     */
    public function getUser($key = null)
    {
        if (null !== $key) {
            return $this->adminAuth->user()->$key;
        }

        return $this->adminAuth->user()->name;
    }

    /**
     * 去掉反斜线
     *
     * @param string $pageData
     *
     * @return bool|string
     */
    public function getPage(string $pageData)
    {
        return substr($pageData, 1);
    }

    /**
     * 获取最后一页
     *
     * @param array $page
     *
     * @return mixed
     */
    public function lastPage(array $page)
    {
        return end($page);
    }

    /**
     * 获取数字
     *
     * @param string $string
     *
     * @return mixed
     */
    public function toNumber(string $string)
    {
        preg_match_all('/\d+/', $string, $numbers);

        return $numbers[0][0];
    }

    /**
     * @param array $pageItem
     *
     * @return array
     */
    public function addOption(array $pageItem)
    {
        array_unshift($pageItem, current($pageItem));
        array_push($pageItem, end($pageItem));

        return $pageItem;
    }

    /**
     * 获取左侧Aside的产品<Li>
     *
     * @return string
     */
    public function getProductAside()
    {
        $category = Category::withoutGlobalScope(CategoryScope::class)->where('father_id', '!=', null)->get();
        $html = '';
        foreach ($category as $item) {
            $href = '/admin/category/'.$item->id.'/product';
            $html .= '<li><a class="slide-item"  href="'.$href.'"><span><strong>'.$item->name.'</strong>('.$item->fatherCategory->name.')</span></a></li>'.PHP_EOL;
        }

        return $html;
    }

    /**
     * 获取select 列表
     *
     * @param $category
     * @param null $product
     *
     * @return string
     */
    public function getSelectItem($category, $product = null)
    {
        $validator = $this->validator;

        if (null === $product) {
            $html = $category->configs->map(function ($item) use ($category, $validator) {
                $fuc = $item->config_type.'s';
                $configType = $item->config_type;
                if ('channel' == $item->config_type) {
                    $result = $category->$fuc->filter(function ($item) {
                        return null != $item->father_id;
                    })->map(function ($item) use ($validator, $configType) {
                        $active = '';
                        if ($validator->getValue($configType) == $item->id) {
                            $active = ' selected="selected"';
                        }

                        return '<option'.$active.' value="'.$item->id.'">'.$item->label.'</option>';
                    })->toArray();
                } else {
                    $result = $category->$fuc->map(function ($item) use ($validator, $configType) {
                        $active = '';
                        if ($validator->getValue($configType) == $item->id) {
                            $active = ' selected="selected"';
                        }

                        return '<option'.$active.' value="'.$item->id.'">'.$item->label.'</option>';
                    })->toArray();
                }
                $selectHtml = implode($result, '');
                $error = PHP_EOL;
                if ($validator->getError($configType)) {
                    $error = '<div class="alert alert-warning" role="alert">'.$validator->getErrors($configType).'</div>';
                }
                $html = '    <div class="form-group">
                        <label for="exampleInputPassword">'.$item->label.'</label>
                        <select class="form-control" name="'.$item->config_type.'">'.$selectHtml.'  </select>
                    </div>'.$error;

                return $html;
            })->toArray();
        } else {
            $html = $category->configs->map(function ($item) use ($category, $product) {
                $fuc = $item->config_type.'s';
                $configType = $item->config_type;

                if ('channel' == $item->config_type) {
                    $result = $category->$fuc->filter(function ($item) {
                        return null != $item->father_id;
                    })->map(function ($item) use ($configType, $product) {
                        $active = '';
                        if ($product->$configType->id == $item->id) {
                            $active = ' selected="selected"';
                        }

                        return '<option'.$active.' value="'.$item->id.'">'.$item->label.'</option>';
                    })->toArray();
                } else {
                    $result = $category->$fuc->map(function ($item) use ($configType, $product) {
                        $active = '';

                        if ($product->$configType->id == $item->id) {
                            $active = ' selected="selected"';
                        }

                        return '<option'.$active.' value="'.$item->id.'">'.$item->label.'</option>';
                    })->toArray();
                }
                $selectHtml = implode($result, '');

                $html = '    <div class="form-group">
                        <label for="exampleInputPassword">'.$item->label.'</label>
                        <select class="form-control" name="'.$item->config_type.'">'.$selectHtml.'  </select>
                    </div>';

                return $html;
            })->toArray();
        }

        $html = implode($html, '');

        return $html;
    }

    /**
     * 获取产品的信息
     *
     * @param Product $product
     *
     * @return string
     */
    public function tableConfig(Product $product)
    {
        $detail = $product->category->configs->map(function ($item) use ($product) {
            $config = $item->config_type;

            return '<strong>'.$item->label.': </strong>&nbsp&nbsp&nbsp'.$product->$config->label.'<br>';
        })->toArray();
        $html = implode($detail, '');

        return $html;
    }
}
