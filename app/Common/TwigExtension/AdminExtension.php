<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/13
 * Time: 17:26
 */

namespace App\Common\TwigExtension;

use App\Common\Auth\AdminAuth;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminExtension extends AbstractExtension
{
    protected $adminAuth;

    /**
     * Constructor.
     *
     * @param AdminAuth $adminAuth
     */
    public function __construct(AdminAuth $adminAuth)
    {
        $this->adminAuth = $adminAuth;
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
     * @return array
     */
    public function addOption(array $pageItem)
    {
        array_unshift($pageItem, current($pageItem));
        array_push($pageItem, end($pageItem));

        return $pageItem;
    }
}
