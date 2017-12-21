<?php
/**
 * 模型示例：公共模型
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Common_model
{
    
	public function __construct()
    {
	}
    
	/**
	 * 示例方法
	 * @param   int 	$class 		类别
	 * @return  array 	$res     	结果集
	 */
	public function demo($class)
    {
        $sql = "select id from db where title = 'aaa'";
        $res = $this->db->query($sql)->row_array();
		return $res;
	}
	
}
