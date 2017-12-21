<?php
/**
 * 数据库连接配置信息
 *
 * @package        Hooloo Framework
 * @author         Passerby
 * @version        1.2.2
 * @release        2017.12.21
 */
if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * mysql数据库配置
 *
 * 说明：数据库可配置多个，默认序号为1，配置参数即“db1”，多个可配置“db2”，“db3”...等等
 *       连接数据库示例：
 *          $this->init_db(1);
 *       如连接配置为“db2”的数据库则是：
 *          $this->init_db(2);
 *       括号内序号与参数序号对应相同即可。
 */
$config['db1'] = array(
    'host' => '192.168.0.8',    // 主机地址
    'user' => 'web_user',       // 用户名
    'pwd' => '123456',          // 密码
    'db' => 'test_db',          // 数据库名称
    'charset' => 'utf8mb4'      // 数据库字符集，默认utf8
);

$config['db2'] = array(
    'host' => '192.168.0.7',    // 主机地址
    'user' => 'web_user',       // 用户名
    'pwd' => '123456',          // 密码
    'db' => 'test_db2',         // 数据库名称
    'charset' => 'utf8'         // 数据库字符集，默认utf8
);

/**
 * mongodb配置
 * 多数据库配置请参考上面的mysql配置说明
 */
$config['mongo1'] = 'mongodb://root:123456@192.168.0.9:3717';

/**
 * redis配置
 * 多数据库配置请参考上面的mysql配置说明
 */
$config['redis1'] = array(
    'host' => '192.168.0.9',    // 主机地址
    'port' => '6379',           // 端口
    'user' => 'test',           // 用户名，阿里云为实例id
    'pwd' => '123456'           // 密码
);

/**
 * memcached配置
 * 多数据库配置请参考上面的mysql配置说明
 */
$config['memc1'] = array(
    'host' => '192.168.0.9',    // 主机地址
    'port' => '11211'           // 端口
);
