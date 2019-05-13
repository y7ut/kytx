<?php
/**
 * Banner图数据模型
 * User: YiChu
 * Date: 2019/5/13
 * Time: 18:29
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['title', 'src', 'url', 'id'];
}
