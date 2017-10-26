<?php
/**
                     _oo8oo_                     
                    o8888888o                    
                    88" . "88                    
                    (| -_- |)                    
                    0\  =  /0                    
                  ___/'==='\___                  
                .' \\|     |// '.                
               / \\|||  :  |||// \               
              / _||||| -:- |||||_ \              
             |   | \\\  -  /// |   |             
             | \_|  ''\---/''  |_/ |             
             \  .-\__  '-'  __/-.  /             
           ___'. .'  /--.--\  '. .'___           
        ."" '<  '.___\_<|>_/___.'  >' "".        
       | | :  `- \`.:`\ _ /`:.`/ -`  : | |       
       \  \ `-.   \_ __\ /__ _/   .-` /  /       
   =====`-.____`.___ \_____/ ___.`____.-`=====   
                    `=---=`                      
                                                 
                    i  i  i                      
   ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~   
                    佛祖保佑                     
                                                 
                永不宕机 永无BUG                 
                                                 

 *
 * @package        Hooloo framework
 * @author         Passerby
 * @copyright      Hooloo Team
 * @version        1.2
 * @release        2017.10.10
 */
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

// 授权域名访问 
$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && strstr($origin, SERVER_NAME)) {
    header('Access-Control-Allow-Origin:' . $origin);       
}

// 设置时区
ini_set('date.timezone', 'Asia/Shanghai');

// 页面开始执行时间
define('BEGINTIME', microtime(true));

// 网站根路径
define('BASEPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
// 应用路径
define('APPPATH', BASEPATH . 'app' . DIRECTORY_SEPARATOR);

// 错误日志目录
define('ERRLOG_PATH', BASEPATH . 'data/logs');
// 会话信息保存目录
define('SESSION_SAVE_PATHS', BASEPATH . 'data/session');
// 缓存目录
define('CACHE_PATH', BASEPATH . 'data/cache');

// 模板引擎编译文件输出路径
define('TPL_COMPILE_PATH', BASEPATH . 'data/runtime');
// 变量界定符左
define('TPL_LEFT_SEPERATOR', '<{');
// 变量界定符右
define('TPL_RIGHT_SEPERATOR', '}>');

// AJAX请求标志
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('IS_POST', strtolower($_SERVER['REQUEST_METHOD']) == 'post');
define('IS_GET', strtolower($_SERVER['REQUEST_METHOD']) == 'get');

// 设置会话保存路径
ini_set('session.save_path', SESSION_SAVE_PATHS);
if (! session_id()) {
    session_start();
}

// 开发环境调试模式，正式环境下设为false
define('DEVELOPMENT_ENVIRONMENT', true);

// 错误报告方式
if (DEVELOPMENT_ENVIRONMENT == true) {
    // 开发环境打印所有错误
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
} else {
    // 正式环境记录错误到日志文件
    error_reporting(E_ALL & ~E_NOTICE);
    ini_set('display_errors', 'On');
    ini_set('log_errors', 'On');
    ini_set('error_log', ERRLOG_PATH . '/error.log');
}

// 加载主文件
require APPPATH . 'core/kernel.php';
