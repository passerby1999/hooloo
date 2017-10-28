<?php
/**
 * 数据库连接类
 *
 * @package        Hooloo framework
 * @author         Bill, Passerby
 * @copyright      Hooloo Team
 * @version        1.2
 * @release        2017.10.27
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Database
{
    private $db_handle;
    private $db_result;
    
    // 连接数据库
    public function connect($address, $username, $password, $db_name)
    {
        $this->db_handle = new mysqli($address, $username, $password, $db_name);
        $this->db_handle->set_charset('utf8');
    }
    
    // 自定义SQL查询语句
    public function query($sql)
    {
        $this->db_result = $this->db_handle->query($sql);
        return $this;
    }
    
    // 返回单条数据
    public function row_array()
    {
        if (false !== $this->db_result) {
            $res = $this->db_result->fetch_assoc();
            $this->db_result->free();
        } else {
            $res = array();
        }
        return $res;
    }
    
    // 返回多条数据
    public function result_array()
    {
        $res = array();
        if (false !== $this->db_result) {
            while($row = $this->db_result->fetch_assoc()) {
                $res[] = $row;
            }
            $this->db_result->free();
        }
        return $res;
    }
    
    // 返回最后插入id
    public function insert_id()
    {
        return $this->db_handle->insert_id;
    }
    
    // 返回影响函数
    public function affected_rows() {
        return $this->db_handle->affected_rows;
    }
    
    // 开启事务
    public function trans_begin()
    {
        // 设置为不自动提交，因为MYSQL默认立即执行
        mysqli_query($this->db_handle, 'SET AUTOCOMMIT = 0');
        // 开始事务定义
        mysqli_begin_transaction($this->db_handle);
    }
    
    // 回滚事务
    public function trans_rollback()
    {
        // 判断当执行失败时回滚
        mysqli_query($this->db_handle, 'ROLLBACK');
    }
    
    // 提交事务
    public function trans_commit()
    {
        // 执行事务
        mysqli_commit($this->db_handle);
    }
    
    // 关闭数据库连接
    public function close()
    {
        $this->db_handle->close();
    }
    
    // 返回MySql版本
    public function getserver_info()
    {
        return mysqli_get_server_info($this->db_handle);
    }
}
