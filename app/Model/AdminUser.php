<?php

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['name', 'email', 'password', 'status', 'last_login'];

    /** @var string 表名 */
    protected $table = 'kytx.admin_user';

    /**
     * 应该转换为日期格式的属性.
     *
     * @var array
     */
    protected $dates = [
        'last_login',
    ];

    /**
     * 获取上次登录的日期.
     *
     * @param string $value
     *
     * @return string
     */
    public function getLastLoginAttribute($value)
    {
        return Carbon::create($value)->toDateString();
    }

    /**
     * 设置用户的密码.
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = md5($value);
    }

    /**
     * 一对多关联文章资讯
     */
    public function news(){
        $this->hasMany(News::class);
    }
}
