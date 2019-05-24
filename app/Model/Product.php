<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/21
 * Time: 11:08
 */

namespace App\Model;

use App\Model\Scope\CategoryScope;
use App\Model\Scope\ChannelScope;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['name', 'intro', 'content', 'image', 'status'];

    public function skill()
    {
        return $this->belongsTo(Skill::class);
    }

    public function output()
    {
        return $this->belongsTo(Output::class);
    }

    public function voice()
    {
        return $this->belongsTo(Voice::class);
    }

    public function type()
    {
        return $this->belongsTo(Types::class, 'type_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class)->withoutGlobalScope(ChannelScope::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withoutGlobalScope(CategoryScope::class);
    }
}
