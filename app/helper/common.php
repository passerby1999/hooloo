<?php
/**
 * 公共辅助函数
 *
 * @package        Hooloo
 * @author         Passerby, Bill
 * @version        1.2.1
 * @release        2017.11.17
 */
if (! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Ajax方式返回数据到客户端
 * @param   array      $data      要返回的数据
 * @param   string     $type      AJAX返回数据格式
 * @param   return     void       结果集
 */
function ajax_return($status = 1, $msg = '', $data = [])
{
    $return = compact('status', 'msg', 'data');
    header('Content-Type:application/json; charset=utf-8');
    exit(json_encode($return, JSON_UNESCAPED_UNICODE));
}

/**
 * 网址重定向
 * @param    string    $uri       要跳转的网址
 * @param    string    $method    跳转方法
 * @param    int       $code      状态码
 */
function redirect($uri = '', $method = 'auto', $code = null)
{
    if (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE) {
        $method = 'refresh';
    } elseif ($method !== 'refresh' && (empty($code) || ! is_numeric($code))) {
        if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
            $code = ($_SERVER['REQUEST_METHOD'] !== 'GET') ? 303 : 307;
        } else {
            $code = 302;
        }
    }
    if ('refresh' == $method) {
        header('Refresh: 0;url=' . $uri);
    } else {
        header('Location: ' . $uri, true, $code);
    }
    exit;
}

/**
 * 生成随机码
 * @param  int      $len     随机码长度
 * @param  string   $type    随机码类型：num-数字，str-小写字母，astr-大写字母，
 *                                       both-小写字母和数字，all-全部字符
 * @return string   $result  返回随机码
 */
function rand_code($len = 6, $type = 'num')
{
    $num = '0123456789';
    $str = 'abcdefghijklmnopqrstuvwxyz';
    $astr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    switch ($type) {
        case 'num':
        case 'str':
        case 'astr':
            $s = $$type;
            break;
        case 'both':
            $s = $str . $num;
            break;
        default:
            $s = $astr . $str . $num;
    }
    $res = '';
    $max = strlen($s) - 1;
    for ($i = 0; $i < $len; $i++) {
        $res .= $s[rand(0, $max)];
    }
    return $res;
}

/**
 * 获取客户端IP地址
 * @param      int     $type   返回类型：0-返回IP地址字符串，1-返回IPV4地址数字
 * @return     mixed           返回结果
 */
function get_client_ip($type = 1)
{
    $ip = '';
    if (isset($_SERVER['HTTP_X_CLIENTIP'])) {
        $ip = $_SERVER['HTTP_X_CLIENTIP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos) unset($arr[$pos]);
        $ip = trim($arr[0]);
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    }
    if ($ip == '::1') $ip = '127.0.0.1';
    if ($type == 1) $ip = sprintf('%u', ip2long($ip));
    return $ip;
}

/**
 * dump                      打印变量
 * @param    mixed  $var     变量
 * @return   void            无返回结果
 */
function dump($var)
{
    ob_start();
    var_dump($var);
    $output = ob_get_clean();
    $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
    $output = '<pre>' . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
    echo($output);
}

/** 
 * 模拟http请求
 * @param     string    $url      请求地址 
 * @param     array     $data     POST数据：如果http请求方式为post，
                                  提交数据格式为数组，如没有提交数据，
                                  参数可写为字符串'post'
 * @param     array     $head     HTTP头字段：格式： array('Content-type: text/plain', 'Content-length: 100')
 * @return    string    $res      返回网页内容：无效网址返回false
 */
function http($url, $data = null, $head = null)
{
    $curl = curl_init(); // 初始化一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 设置要访问的地址
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 设置为 1 是检查服务器SSL证书中是否存在一个公用名(common name)。译者注：公用名(Common Name)一般来讲就是填写你将要申请SSL证书的域名 (domain)或子域名(sub domain)。 设置成 2，会检查公用名是否存在，并且是否与提供的主机名匹配。 0 为不检查名称。 在生产环境中，这个值应该是 2（默认值）
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer 
    if ($data){
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求 
        if (is_array($data)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包  
        }
    }
    if ($head) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, $head);
    }
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环  
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); 
    $res = curl_exec($curl); // 执行操作
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE); // 获取http状态码
    curl_close($curl); // 关闭CURL会话
    if($httpcode == 404 || $httpcode == 403) {
      return false;
    }else{
      return $res;
    }
}

/**
 * 日期格式化
 * @param   date    $time       时间 Y-m-d H:i:s 或者时间戳
 * @param   int     $type       返回时间格式：1-显示短日期：8-21，9：00；0-时间差格式：3天前，2小时前
 * @param   string  $result     返回值
 */
function time_format($time = 0, $type = 0)
{
    $result = '';
    if (! $time) {
        $times = time();
    } else {
        //转换为时间戳
        $times = is_numeric($time) ? $time : strtotime($time);
    }
    if ($type == 1) {
        if (date('Y-m-d', $times) == date('Y-m-d')) {
            //今天
            $result = date('H:i', $times);
        } elseif (date('Y', $times) == date('Y')) {
            //今年
            $result = date('m-d',$times);
        } else {
            //其他年份
            $result = date('Y',$times);
        }
    } else {
        if (date('Y-m-d', $times) == date('Y-m-d')) {
            //今天
            $timediff = floor((time() - $times) / 60); //时间差：分钟
            if ($timediff > 59) {
                $result = floor($timediff / 60) . '小时前';
            } elseif ($timediff > 0) {
                $result = $timediff . '分钟前';
            } else {
                $result = '刚刚';
            }
        } elseif (date('Y-m-d', $times + 86400) == date('Y-m-d')) {
            $result = '昨天';
        } elseif (date('Y-m-d', $times + 86400 * 2) == date('Y-m-d')) {
            $result = '前天';
        } else {
            $timediff = floor((time() - $times) / 86400); //时间差：天
            if ($timediff > 365) {
                $result = floor($timediff / 365) . '年前';
            } elseif ($timediff > 30) {
                $result = floor($timediff / 30) . '个月前';
            } else {
                $result = $timediff . '天前';
            }
        }
    }
    return $result;
}

/**
 * 格式化插入数据库的特殊字符串，如用户名，邮箱，时间等
 * @param       string $str     要转换的字符串
 * @return      string $str     返回结果集
 */
function sql_format($str)
{
    //过滤用户输入
    $str = str_format($str);
    //删除非法字符
    $str = str_replace("'", '', $str);
    $str = str_replace('&', '', $str);
    $str = str_replace('=', '', $str);
    $str = str_replace('\"', '', $str);
    $str = str_replace('\\', '', $str);
    
    return $str;
}

/**
 * 短字符串过滤函数，如过滤文章的标题等单行文本
 * @param   string  $str        要过滤的字符串
 * @return  string  $str        返回结果集
 */
function str_format($str)
{
    //html转义字符
    $str = str_ireplace('&amp;', '&', $str);
    $str = str_ireplace('&nbsp;', ' ', $str);
    $str = str_ireplace('&quot;', '\"', $str);
    $str = str_ireplace('&lt;', '<', $str);
    $str = str_ireplace('&gt;', '>', $str);
    $str = str_ireplace('&#8206;', '', $str);
    //过滤用户输入
    $str = strip_tags($str);
    //删除多余空格
    $str = preg_replace('/\s+/', ' ', $str);
    //删除多余单引号
    $str = str_replace("\\", '', $str);
    $str = preg_replace('/\'+/', "'", $str);
    $str = str_replace("'", "''", $str);
    //过滤字符串首尾空格
    $str = trim($str);
    
    return $str;
}

/**
 * 文本过滤函数
 * @param   string  $str        要过滤的文本
 * @return  string  $str        返回结果集
 */
function text_format($str)
{
    //兼容不规范换行符
    $str = preg_replace('/<br\s?\/?>/i', PHP_EOL, $str);
    //过滤用户输入
    $str = strip_tags($str);
    //替换回换行符
    $str = str_replace(PHP_EOL, '<br />', $str);
    $str = preg_replace('/\s*<br \/>\s*/', '<br />', $str);
    //删除多余单引号
    $str = str_replace("\\", '', $str);
    $str = preg_replace('/\'+/', "'", $str);
    $str = str_replace("'", "''", $str);
    //过滤字符串首尾空格
    $str = trim($str);
    
    return $str;
}

/**
 * 自动解析编码读入文件
 * @param   string      $file       文件路径
 * @param   string      $charset    读取文件的目标编码
 * @return  string|false            返回读取内容，文件不存在返回false
 */
function read_file($file, $charset = 'UTF-8') {
    if (file_exists($file)) {
        $str = file_get_contents($file);
        if ($str) {
            $arr = ['GBK', 'UTF-8', 'UTF-16LE', 'UTF-16BE', 'ISO-8859-1'];
            $enc = mb_detect_encoding($str, $arr, true);
            if ($charset != $enc) {
                $str = mb_convert_encoding($str, $charset, $enc);
            }
        }
        return $str;
    }
    return false;
}
    
/**
 * 多维数组按某一列的值排序
 * @param   $array  array               要排序的数组
 * @param   $key    string|int          排序的键，如是数值则按数字索引排序
 * @param   $sort   SORT_ASC|SORT_DESC  排序方式，默认升序(4)，降序为(3)
 * @return          array|false         返回排序后的数组，排序失败返回false
 */
function multi_array_sort($array = [], $key = 0, $sort = SORT_ASC)
{
    if (is_array($array)) {
        $arr1 = array_column($array, $key);
        if ($arr1) {
            array_multisort($arr1, $sort, $array);
            return $array;
        }
    }
    return false;
}
