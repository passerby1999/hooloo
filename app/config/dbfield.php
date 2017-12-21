<?php
/**
 * 分类数组配置文件
 *
 * @package        Hooloo Framework
 * @author         Passerby
 * @version        1.2
 * @release        2017.10.10
 *
 * 说明：
 *      把常用分类数组从数据库保存在配置文件中，以加快载入速度。
 * 
 * 注意：
 *      除数据库和路由配置外的其他配置文件在使用时需要手工指定载入。
 *
 *      加载赋值：
 *          $config = $this->load_config('dbfield');
 *      一次加载多个配置文件：
 *          $config = $this->load_config('dbfield', 'dbfield1');
 */
if (! defined('BASEPATH')) exit('No direct script access allowed');

$config["cat_gender"] = array(
	'0' => '未选择',
	'1' => '男',
	'2' => '女',
	'3' => '混合'
);
