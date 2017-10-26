<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 配置文件
 *
 * @package        Hooloo framework
 * @author         Passerby
 * @copyright      Hooloo Team
 * @version        1.2
 * @release        2017.10.10
 */

// 服务器域名，正式环境主机名称，不带http
define('SERVER_NAME', "www.test.com");
// web服务器地址
define('WEB_SERVER', 'http://' . SERVER_NAME);
// 静态文件css/img/js服务器地址
define('STATIC_SERVER', '/static');

// 加载数据库配置
require APPPATH . 'config/database.php';

// 加载路由配置
require APPPATH . 'config/routes.php';

// Memcached缓存时间
// $config['mc_intro_expire'] = 86400 * 3; //3天
// $config['mc_other_expire'] = 86400 * 3; //3天
