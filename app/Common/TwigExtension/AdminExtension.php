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
        ];
    }

    /**
     * 获取用户的用户名（email）
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

        return $this->adminAuth->user()->email;
    }
}