<?php
/**
 * 图片处理函数
 *
 * @package        Hooloo framework
 * @author         Passerby
 * @version        1.2
 * @release        2017.11.10
 */
if (! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * 图片缩放
 * @param   string  $source_image   源文件路径加文件名(相对路径)
 * @param   string  $new_image      新文件路径加文件名(相对路径)
 * @param   string  $width          图片宽度
 * @param   string  $height         图片高度
 * @return  boolean                 返回处理结果
 */
function img_resize($source_image = '', $new_image = '', $width = 0, $height = 0)
{
    if (! file_exists($source_image) || ! $new_image || ! $width || ! $height) return false;
    $conf = [
        'source_image' => $source_image, //图片原始路径
        'new_image' => $new_image, //新图片地址(不能使用URL 。为空时新图片覆盖老图片)
        'thumb_marker' => '', //指定缩略图后缀
        'width' => $width,
        'height' => $height,
        'quality' => "75%" //图片品质
    ];
    $gd = new Image_lib($conf);
    if ($gd->resize()) {
        return true;
    }
    return false;
}

/**
 * 图片旋转
 * @param    string    $source_image    源文件路径加文件名(相对路径)
 * @param    string    $angle           旋转角度
 * @return   boolean                    处理结果
 */
function img_rotate($source_image = '', $angle = 0)
{
    if (! file_exists($source_image) || ! $angle) return false;
    if ($angle > 0) {
        $angle = 360 - $angle;
    } else {
        $angle = - $angle;
    }
    $conf = [
        'source_image' => $source_image, //图片原始路径
        'rotation_angle' => $angle, //旋转角度
        'quality' => '100%' //图片品质
    ];
    $gd = new Image_lib($conf);
    if ($gd->rotate()) {
        return true;
    }
    return false;
}

/**
 * 添加图片水印
 * @param   string  $source_image   源文件路径加文件名(相对路径)
 * @param   string  $width          图片宽度
 * @return  boolean                 返回处理结果
 */
function img_watermark($source_image = '', $width = 0)
{
    if (! file_exists($source_image)) return false;
    // 获取图片宽度
    if (! $width) {
        if ($info = getimagesize($source_image)) {
            $width = $info[0];
        } else {
            return false;
        }
    }
    // 根据不同图片宽度选择相应大小水印
    if ($width > 800) {
        $water_size = 1000;
        $offset = 15;
    } elseif ($width > 600) {
        $water_size = 800;
        $offset = 10;
    } elseif ($width > 450) {
        $water_size = 600;
        $offset = 7;
    } else {
        $water_size = 450;
        $offset = 5;
    }
    $water_pic = BASEPATH . "upload/water/" . $water_size . ".png";
    //水印通用配置
    $conf = [
        'source_image' => $source_image, // 图片原始路径
        'quality' => "90%", // 图片品质
        'wm_type' => 'overlay', // 水印类型 text文字,overlay图片水印
        'wm_vrt_alignment' => 'bottom', // 设置水印图像的垂直对齐方式 top, middle, bottom
        'wm_hor_alignment' => 'right', // 置水印图像的水平对齐方式。left, center, right
        'wm_hor_offset' => $offset, // 水平偏移量
        'wm_vrt_offset' => $offset, // 垂直偏移量
        'wm_padding' => '0', // 内边距
        'wm_overlay_path' => $water_pic, // 水印图片
        'wm_opacity' => '50', //透明度
        'wm_x_transp' => '1',
        'wm_y_transp' => '1'
    ];
    $gd = new Image_lib($conf);
    if ($gd->watermark()) {
        return true;
    }
    return false;
}
