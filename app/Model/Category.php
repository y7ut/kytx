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
use App\Model\Scope\ChannelSonScope;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['name', 'label'];

    /**
     * 模型的「启动」方法
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CategoryScope());
    }

    /**
     * 获取这个产品分类所属于的大产品分类
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function fatherCategory()
    {
        return $this->belongsTo(Category::class, 'father_id')->withoutGlobalScope(CategoryScope::class);
    }

    /**
     * 获取这个大产品分类全部拥有的产品分类
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany(Category::class, 'father_id')->withoutGlobalScope(CategoryScope::class);
    }

    /**
     * 获取全部的子产品分类
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allChildrenCategories()
    {
        return $this->categories()->with('allChildrenCategories');
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function types()
    {
        return $this->hasMany(Types::class);
    }

    public function channels()
    {
        return $this->hasMany(Channel::class)->withoutGlobalScope(ChannelScope::class);
    }

    public function sizes()
    {
        return $this->hasMany(Size::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function configs()
    {
        return $this->hasMany(Config::class);
    }
}
