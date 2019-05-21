<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/15
 * Time: 17:35
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['title', 'img', 'type', 'content', 'hot'];

    /**
     * 被user关联
     */
    public function user()
    {
        return $this->belongsTo(AdminUser::class);
    }
}
