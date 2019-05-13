<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['account', 'password', 'status', 'last_login'];

    /** @var string 表名 */
    protected $table = 'kytx.admin_user';

    /**
     * 设置用户的密码.
     *
     * @param  string  $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = md5($value);
    }

}

