<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/16
 * Time: 14:29
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    /** @var array 数组中的属性会被展示 */
    protected $visible = ['title', 'max_wage', 'min_wage', 'address', 'max_experience','min_experience','education','work_category','job_intro','work_intro','status'];
}

