<?php
/**
 * 模型示例：图片模型
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Pic_model
{
    public function __construct()
    {
    }
    
    /**
     * 获取图片信息
     *
     * @param   int     $class      类别
     * @param   int     $id         图片id
     * @return  array   $result     结果集
     */
    public function get_pic($class, $id)
    {
        $result = array();
        if ($class > 0 && $id > 0) {
            $sql = "select title, url, width, height, description from picture where id = $id and class = $class";
            $result = $this->db->query($sql)->row_array();
        }
        return $result;
    }
    
}
