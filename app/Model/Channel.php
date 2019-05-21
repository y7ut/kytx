<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/21
 * Time: 10:49
 */

namespace App\Model;

use App\Model\Scope\CategoryScope;
use App\Model\Scope\ChannelScope;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['name', 'label'];

    /**
     * 模型的「启动」方法
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ChannelScope());
    }

    /**
     * 获取这个频段所属于的产品大分类
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class)->withoutGlobalScope(CategoryScope::class);
    }

    /**
     * 获取这个频段所属于的大频段
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fatherChannel()
    {
        return $this->belongsTo(Channel::class, 'father_id')->withoutGlobalScope(ChannelScope::class);
    }

    /**
     * 获取这个大频段全部拥有的频段
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function channels()
    {
        return $this->hasMany(Channel::class, 'father_id')->withoutGlobalScope(ChannelScope::class);
    }

    /**
     * 获取全部的子频段
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allChildrenChannels()
    {
        return $this->channels()->with('allChildrenChannels');
    }
}
