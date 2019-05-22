<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/21
 * Time: 10:49
 */

namespace App\Model;

use App\Model\Scope\CategoryScope;
use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['name', 'label'];

    public function category()
    {
        return $this->belongsTo(Category::class)->withoutGlobalScope(CategoryScope::class);
    }
}
