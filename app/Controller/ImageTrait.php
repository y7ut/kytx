<?php
/**
 * Created by PhpStorm.
 * User: YiChu
 * Date: 2019/5/14
 * Time: 13:55
 */

namespace App\Controller;

use Intervention\Image\ImageManagerStatic as Image;
use Slim\Http\UploadedFile;

trait ImageTrait
{
    /**
     * 图像重新压缩 需要删除临时的文件图片
     *
     * @param UploadedFile $file
     * @param int          $num    图像重新压缩百分比
     * @param array        $resize
     *
     * @return string 图像的新路径/名字
     */
    public function uploadImage(UploadedFile $file, int $num = 60, array $resize = [])
    {
        //使用 intervention/image 扩展进行压缩
        $img = Image::make($file->file);
        if (!empty($resize)) {
            list($heigth, $width) = $resize;
            $img->resize($heigth, $width);
        }
        $type = substr($file->getClientMediaType(), strpos($file->getClientMediaType(), '/') + 1);
        $fileName = ''.'image'.'/'.time().rand(1000, 9999).'.'.$type;
        $img->save($fileName, $num);

        return $fileName;
    }

    /**
     * 销毁图片文件
     *
     * @param string $file 文件路径
     *
     * @return bool
     */
    public function delImage($file)
    {
        return unlink($file);
    }
}
