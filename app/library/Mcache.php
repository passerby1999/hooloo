<?php
/**
 * 高速缓存类
 *
 * 功能：利用redis实现高速缓存
 * 使用示例：
        //实例化类
          $mc = new Mcache($redis);
        //写入缓存，$key为任意可打印字符，$val为字符串或数组，$expire为过期时间，单位秒，默认86400
          $mc->set($key, $val, $expire);
        //读取缓存
          $res = $mc->get($key);
        //删除缓存
          $md->delete($key);
 *
 * @package        Hooloo
 * @author         Passerby
 * @version        1.2
 * @release        2017.10.10
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Mcache
{
    private $_redis;
    
	public function __construct($redis)
    {
		$this->_redis = $redis;
	}
    
    public function set($key, $val, $expire)
    {
        if ($key) {
            $key = 'mc:' . $key;
            if ($this->_redis->setex($key, $expire, json_encode($val, JSON_UNESCAPED_UNICODE))) {
                return true;
            }
        }
        return false;
    }
    
    public function get($key)
    {
        $key = 'mc:' . $key;
        $res = $this->_redis->get($key);
        if ($res) {
            return json_decode($res, true);
        }
        return false;
    }
    
    public function delete($key)
    {
        $key = 'mc:' . $key;
        return $this->_redis->del($key);
    }
}
