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
use App\Model\Scope\CategoryScope;
use App\Model\Scope\ChannelScope;
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
            return  $this->adminAuth->user()->$key;
        }

        return $this->adminAuth->user()->name;
    }

    public function getPage($pageData)
    {
        return substr($pageData, 1);
    }

    public function lastPage(array $page)
    {
        return end($page);
    }

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

    public function getSelectItem($category)
    {   $validator = $this->validator;
        $html = $category->configs->map(function ($item) use ($category,$validator){
            $fuc = $item->config_type.'s';
            $configType = $item->config_type;
//            if($fuc == 'channels'){
//                $model = $category->$fuc->withoutGlobalScope(ChannelScope::class);
//            }else{
//                $model = $category->$fuc;
//            }
            $result = $category->$fuc->map(function ($item) use ($validator, $configType){
                $active = '';
                if ($validator->getValue($configType)==$item->id){
                    $active = ' selected="selected"';
                }
                return '<option'.$active.' value="'.$item->id.'">'.$item->name ." : ". $item->label.'</option>';
            })->toArray();

            $selectHtml = implode($result,'');
            $error = PHP_EOL;
            if ($validator->getError($configType)){
                $error = '<div class="alert alert-warning" role="alert">'.$validator->getErrors($configType).'</div>';
            }
            $html = '    <div class="form-group">
                        <label for="exampleInputPassword">'.$item->label.'</label>
                        <select class="form-control" name="'.$item->config_type.'">'.$selectHtml.'  </select>
                    </div>'.$error;
            return $html;
        })->toArray();

        $html = implode($html,'');

        return $html;
    }
}
