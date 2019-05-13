<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/13
 * Time: 15:12
 */

namespace App\Common\Auth;


use App\Model\AdminUser;
use http\Exception\RuntimeException;
use Psr\Http\Message\RequestInterface;
use SlimSession\Helper;

class AdminAuth
{
    /**
     * @var AdminUser  登录的用户实例
     */
    protected $authUser;

    /**
     * @var Helper 驱动
     */
    protected $authDrive;

    public function __construct(Helper $drive)
    {
        $this->authDrive = $drive;
        $this->authUser = null;
    }

    /**
     * 登录
     *
     * @param RequestInterface $request
     * @param array $paramsTitle
     * @return AdminUser|null
     */
    public function login(RequestInterface $request, array $paramsTitle= ['email', 'password']) : ?AdminUser
    {
        if(!is_null($this->authUser)){
            if ($this->authDrive->get('Admin_Auth_Session_'.$this->authUser->id)){
                throw new \RuntimeException('can\'t login in again');
            }
        }

        $params = $request->getParams($paramsTitle);

        $user = AdminUser::where([
            'email' => $params['email'],
            'password' => md5($params['password'])
        ])->get()->first();
        if($user){
            $this->authUser = $user;
            $this->authDrive->set('Admin_Auth_Session',$this->authUser);
            return $user;
        }else{
            return null;
        }
    }

    /**
     * 注销
     *
     */
    public function logout() :void
    {
        $this->authDrive->delete('Admin_Auth_Session');
    }


    /**
     * 获取当前用户的实例
     *
     * @return AdminUser|null
     */
    public function user() : ?AdminUser
    {
        if(!is_null($this->authUser = $this->authDrive->get('Admin_Auth_Session'))){
            return $this->authUser;
        }
        return null;
    }

    /**
     * f获取当前用户的ID
     *
     * @return int|null
     */
    public function id(): ?int
    {
        if(!is_null($this->authUser = $this->authDrive->get('Admin_Auth_Session'))){
            return $this->authUser->id;
        }
        return null;
    }
}
