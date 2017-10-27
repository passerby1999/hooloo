<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * URI路由配置
 *
 * @package        Hooloo framework
 * @author         Passerby
 * @copyright      Hooloo Team
 * @version        1.2
 * @release        2017.10.10
 *
 * 示例：
 *         $route['music/(:num)'] = 'music/home/$1';
 *         $route['(:num)/(:num)/(:num).html'] = 'music/list/$1/$2/$3';
 * 说明：
 *        (:num)    匹配只含有数字的一段 
 *        (:any)    匹配含有任意字符的一段（除了 '/' 字符）
 *        (:left)   左侧顶格
 *        (:right)  右侧顶格
 */

$route['music/(:num)'] = 'music/home/$1';
$route['(:num)/(:num)/(:num).html'] = 'music/list/$1/$2/$3';
