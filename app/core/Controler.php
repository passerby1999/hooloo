<?php
/**
 * 主控制器
 *
 * @package        Hooloo Framework
 * @author         Passerby, Bill
 * @version        1.2
 * @release        2017.10.10
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Controller 
{
    protected $db;       // mysql数据库
    protected $redis;    // redis数据库
    protected $mongo;    // mongo数据库
    protected $memc;     // memcached数据库
    protected $user;     // 用户信息
    protected $tpl_data; // 模板数据
    
    public function __construct()
    {
        // 默认加载公共函数
        $this->load_helper('common');
    }
    
    public function __destruct()
    {
        // 关闭数据库连接
        if ($this->db) {
            $this->db->close();
        }
    }
    
    /**
     * 加载配置文件
     * @param   string  $confs     配置文件名称，全部小写，可接受多个参数
     */
    protected function load_config(...$confs)
    {
        global $config;
        foreach ($confs as $conf) {
            $conf = strtolower($conf);
            // 配置文件名必须全部小写
            if (file_exists(APPPATH . 'config/' . $conf . '.php')) {
                include_once APPPATH . 'config/' . $conf . '.php';
            } else {
                show_error(994, ['title' => "The configure file <i>$conf</i> does not exist."]);
            }
        }
        return $config;
    }
    
    /**
     * 加载辅助函数
     * @param   string  $load_helper     辅助函数文件名称，全部小写
     */
    protected function load_helper($helper = '')
    {
        if (! $helper) {
            $helper = 'common';
        } else {
            $helper = strtolower($helper);
        }
        // 辅助函数名必须全部小写
        if (file_exists(APPPATH . 'helper/' . $helper . '.php')) {
            include_once APPPATH . 'helper/' . $helper . '.php';
        } else {
            show_error(995, ['title' => "The helper <i>$helper</i> does not exist."]);
        }
    }
    
    /**
     * 加载模型
     * @param   string  $model     模型名称，全部小写
     */
    protected function load_model($model = '')
    {
        global $controller;
        if (! $model) {
            // 参数为空则默认加载与当前控制器同名model
            $model = $controller . '_model';
        } else {
            $model = strtolower($model);
        }
        // 模型文件名必须首字母大写、其他小写
        $model_name = ucwords($model);
        if (file_exists(APPPATH . 'model/' . $model_name . '.php')) {
            include_once APPPATH . 'model/' . $model_name . '.php';
            $this->$model = new $model_name();
            // 在模型中继承控制器的属性
            foreach (array_keys(get_object_vars($this)) as $var) {
                $this->$model->$var = $this->$var;
            }
        } else {
            show_error(996, ['title' => "The model <i>$model_name</i> does not exist."]);
        }
    }
    
    /**
     * 初始化数据库连接
     * @param    int     $server_id    服务器id    1-主服务器；2-从服务器
     */
    protected function init_db($server_id = 1)
    {
        if (! $this->db) {
            global $config;
            $conf = $config['db' . $server_id];
            $this->db = new Database();
            $this->db->connect($conf['host'], $conf['user'], $conf['pwd'], $conf['db'], $conf['charset']);
            $GLOBALS['db'] = $this->db;
        }
    }
    
    /**
     * 初始化mongodb连接
     * @param    int     $server_id    服务器id    1-主服务器
     */
    protected function init_mongo($server_id = 1)
    {
        if (! $this->mongo) {
            global $config;
            //连接主数据库，mongo会默认使用已打开的连接
            $this->mongo = new MongoClient($config['mongo' . $server_id]);
            $GLOBALS['mongo'] = $this->mongo;
        }
    }
    
    /**
     * 初始化redis连接
     * @param    int     $server_id    服务器id    1-主服务器；2-从服务器
     */
    protected function init_redis($server_id = 1)
    {
        if (! $this->redis) {
            global $config;
            $conf = $config['redis' . $server_id];
            //创建相同的新连接时，redis会默认使用已打开的连接
            $this->redis = new Redis();
            $this->redis->connect($conf['host'], $conf['port']);
            //阿里云服务器需要验证用户
            $this->redis->auth($conf['user'] . ':' . $conf['pwd']);
            $GLOBALS['redis'] = $this->redis;
        }
    }
    
    /**
     * 初始化memcached连接
     * @param    int     $server_id    服务器id    1-主服务器
     */
    protected function init_memc($server_id = 1)
    {
        if (! $this->memc) {
            $this->memc = new Memcached;
            //建立连接前，先判断
            if (count($this->memc->getServerList()) == 0) {
                global $config;
                $conf = $config['memc' . $server_id];
                //所有option都要放在判断里面，因为有的option会导致重连，让长连接变短连接！
                $this->memc->setOption(Memcached::OPT_COMPRESSION, false); //关闭压缩功能
                $this->memc->setOption(Memcached::OPT_BINARY_PROTOCOL, true); //使用binary二进制协议
                // addServer 代码必须在判断里面，否则相当于重复建立’ocs’这个连接池，可能会导致客户端php程序异常
                $this->memc->addServer($conf['host'], $conf['port']);
            }
        }
        $GLOBALS['memc'] = $this->memc;
    }
    
    /**
     * 分配变量
     * @param string      $key     标签名
     * @param mix         $val     变量值
     */
    protected function assign($key, $val)
    {
        if ($key && preg_match('/^[A-z_][A-z0-9_]*$/', $key)) {
            $this->tpl_data[$key] = $val;
        } else {
            exit('变量名不合法：' . $key);
        }
    }

    /**
     * 页面输出
     */
    protected function display($html = '')
    {
        global $controller, $method;
        $this->tpl_data['_controller'] = $controller;
        $this->tpl_data['_method'] = $method;
        //视图文件
        if (! $html) {
            $html = APPPATH . 'view/' . $controller . '/' . $method . '.html';
        } else {
            $html = APPPATH . 'view/' . $html . '.html';
        }
        //加载模板
        $tpl = new Template($html);
        $tpl->display($this->tpl_data);
        exit;
    }

    /**
    * 输出错误提示
    */
    protected function prt_error($msg = '')
    {
        $this->assign('msg', $msg);
        $this->display('public/error');
    }
}
