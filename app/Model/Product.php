<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/21
 * Time: 11:08
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['name', 'intro', 'content', 'image', 'status'];

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function types()
    {
        return $this->belongsTo(Types::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
