<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 数据库连接配置信息
 *
 * @package        Hooloo framework
 * @author         Passerby
 * @copyright      Hooloo Team
 * @version        1.2
 * @release        2017.10.10
 */

// mysql
$config['db1'] = array(
    'host' => '192.168.0.9', // 主机地址
    'user' => 'root', // 用户名
    'pwd' => '123456', // 密码
    'db' => 'test' // 数据库名称
);
$config['db2'] = array(
    'host' => '192.168.0.9', // 主机地址
    'user' => 'root', // 用户名
    'pwd' => '123456', // 密码
    'db' => 'test2' // 数据库名称
);

// mongodb
$config['mongo1'] = 'mongodb://root:123456@192.168.0.9:3717';

// redis
$config['redis1'] = array(
    'host' => '192.168.0.9', // 主机地址
    'port' => '6379', // 端口
    'user' => 'test', // 用户名，阿里云为实例id
    'pwd' => '123456' // 密码
);

// memcached
$config['memc1'] = array(
    'host' => '192.168.0.9', // 主机地址
    'port' => '11211' // 端口
);
