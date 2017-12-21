<?php
/**
 * 默认控制器
 *
 * @package        Hooloo Framework
 * @author         Passerby
 * @version        1.2.2
 * @release        2017.12.21
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * 默认首页
     */
    public function index()
    {
        // 连接数据库示例 
        $this->init_db(1);
        $sql = "select title from books limit 10";
        $res = $this->db->query($sql)->result_array();
        dump($res);
        
        // 连接mongodb示例
        $this->init_mongo(1);
        $coll = $this->mongo->book->title;
        $query = array('likes' => array('$lt' => 200));
        $field = array('title' => 1, 'likes' => 1);
        $limit = 10;
        $sort = array('likes' => -1);
        $skip = 1;
        $res = iterator_to_array($coll->find($query, $field)->limit($limit)->sort($sort)->skip($skip));
        dump($res);


        // 连接redis示例
        $this->init_redis(1);
        $res = $this->redis->zrange("book:likes", 0, 3);
        dump($res);
        
        // 连接memcached示例
        $this->init_memc(1);
        $res = $this->memc->get("book:intro:123");
        dump($res);
        
        // 加载配置文件示例，数据库和路由配置文件默认自动加载
        $config = $this->load_config('dbfield', 'dbfield1');
        dump($config);
        
        // 加载模型示例，模型中应用到的各类数据库连接应先在控制器中进行初始化
        $this->init_db(1);
        $this->load_model('pic_model');
        $res = $this->pic_model->get_pic(4, 1078079);
        dump($res);
        
        // 加载辅助函数的例子 
        $this->load_helper('chinese');
        dump(mb_strtr('我们和他们', '我们', '你门'));
        
        // 计算页面处理时间
        $runtime = microtime(true);
        echo '页面处理时间：', number_format(($runtime - BEGINTIME) * 1000000), ' 微秒';
    }
    
    /**
     * 输出页面示例
     * 浏览器访问地址：http://www.test.com/index/demo/5/123
     */
    public function demo($class = 0, $id = 0)
    {
        // 接收参数
        // $class = 5;
        // $id = 123;
        
        // 处理你的代码
        $res = [
            'title' => 'Stray Bird';
            'author' => 'Tagore';
            'price' => '22.00';
        ];
        
        // 变量赋值
        $this->assign('class', $class);
        $this->assign('id', $id);
        $this->assign('data', $res);
        
        // 输出页面
        $this->display();
        // $this->display('book/detail'); // 可指定视图文件
    }
    
}
