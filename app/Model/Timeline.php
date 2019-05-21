<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/17
 * Time: 16:09
 */

namespace App\Model;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Timeline extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['title', 'image', 'time', 'sort'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 应该转换为日期格式的属性.
     *
     * @var array
     */
    protected $dates = [
        'time',
    ];

    /**
     * 获取上次登录的日期.
     *
     * @param string $value
     *
     * @return string
     */
    public function getTimeAttribute($value)
    {
        return Carbon::create($value)->toDateString();
    }
}
