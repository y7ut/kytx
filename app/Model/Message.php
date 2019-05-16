<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/15
 * Time: 17:35
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['name', 'email', 'status', 'content'];

}
