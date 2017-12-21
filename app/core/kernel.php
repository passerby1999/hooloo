<?php
/**
 * �����ļ�
 *
 * @package        Hooloo Framework
 * @author         Passerby, Bill
 * @version        1.2.2
 * @release        2017.12.21
 */
if (! defined('BASEPATH')) exit('No direct script access allowed');

// ������
function _error_handler($severity, $message, $filepath, $line)
{
    $is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);
    if (($severity & error_reporting()) !== $severity) {
        return;
    }
    if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors'))) {
        $error_data['severity'] = $severity;
        $error_data['message'] = $message;
        $error_data['filepath'] = $filepath;
        $error_data['line'] = $line;
        $error_data['title'] = 'A PHP Error was encountered';
        show_error(1, $error_data);
    }
    if ($is_error) exit;
}

// �쳣����
function _exception_handler($exception)
{
    if (str_ireplace(array('off', 'none', 'no', 'false', 'null'), '', ini_get('display_errors'))) {
        $error_data['exception'] = $exception;
        $error_data['title'] = 'An uncaught Exception was encountered';
        show_error(2, $error_data);
    }
    exit;
}

// �������󱨸洦��
function _shutdown_handler()
{
    $last_error = error_get_last();
    if (isset($last_error) && ($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING))) {
        _error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
    }
}

// ��ʾ������Ϣ
function show_error($type = 0, $data = array())
{
    $html = '<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Error</title>
<style type="text/css">
body {
    margin: 40px;
    font: 13px/20px normal Helvetica, Arial, sans-serif;
}
h1 {
    color: #444;
    border-bottom: 1px solid #D0D0D0;
    font-size: 19px;
    margin: 0 0 14px 0;
    padding: 14px 15px 10px 15px;
}
#container {
    margin: 10px;
    border: 1px solid #D0D0D0;
    box-shadow: 0 0 8px #D0D0D0;
}
p {
    margin: 12px 15px;
}
</style>
</head>
<body>
    <div id="container">';
    if (DEVELOPMENT_ENVIRONMENT === true) {
        switch ($type) {
            case 1:
                // ������
                $html .= '<h1>' . $data['title'] . '</h1>';
                $html .= '<p>Severity: ' . $data['severity'] . '</p>';
                $html .= '<p>Message: ' . $data['message'] . '</p>';    
                $html .= '<p>Filename: ' . str_replace(BASEPATH, '', $data['filepath']) . '</p>';    
                $html .= '<p>Line: ' . $data['line'] . '</p>';
                
                // AJAX��ʾ
                $msg = 'Filename: ' . str_replace(BASEPATH, '', $data['filepath']) . $data['line'] . ', Message:'. $data['message'];
                break;
            case 2:
                // �쳣����
                $exception = $data['exception'];
                $message = $exception->getMessage();
                if (empty($message)) $message = '(null)';
                $html .= '<h1>' . $data['title'] . '</h1>';
                $html .= '<p>Type: ' . get_class($exception) . '</p>';    
                $html .= '<p>Message: ' . $message . '</p>';    
                $html .= '<p>Filename: ' . $exception->getFile() . '</p>';    
                $html .= '<p>Line Number: ' . $exception->getLine() . '</p>';
                
                // AJAX��ʾ
                $msg = 'Filename: ' . $exception->getFile() . $exception->getLine() . ', Message:'. $message;
                break;
            default:
                // AJAX��ʾ
                $msg = isset($data['title']) ? $data['title'] : 'The page you requested was not found.';
                $msg .= " ($type)";
                
                $html .= "<h1>404 Page Not Found</h1>";
                $html .= "<p>$msg</p>";
        }
    } else {
        $msg = '500 Internal Server Error';
        $html .= "<h1>$msg</h1>";
        $html .= '<p>The server encountered an unexpected condition which prevented it from fulfilling the request.</p>';
    }
    // AJAX������ʾ
    IS_POST && ajaxReturn(0, $msg);
    // ҳ����ʾ����
    $html .= "</div>\n</body>\n</html>";
    exit($html);
}

// �����󷽷�����ҪĿ�Ĳ��URL����
function call_hook()
{
    // ����·��
    $path_info = trim(@$_SERVER['PATH_INFO'], '/');
    // ·�ɽ���
    global $route;
    if ($route) {
        foreach ($route as $k => $v) {
            $k = str_replace('/', '\/', preg_quote($k));
            $k = str_ireplace('\(\:num\)', '(\d+)', $k);
            $k = str_ireplace('\(\:any\)', '([^\/]+)', $k);
            $k = str_ireplace('\(\:left\)', '^', $k);
            $k = str_ireplace('\(\:right\)', '$', $k);
            $k = str_replace('\^', '^', $k);
            $k = str_replace('\$', '$', $k);
            $path_info = preg_replace('/' . $k . '/', $v, $path_info);
        }
    }
    // �����������ͷ���
    if (strpos($path_info, '/')) {
        $arr_path = explode('/', $path_info);
        $controller = $arr_path[0];
        $method = $arr_path[1];
        if (isset($arr_path[2])) {
            $arr_query = array_slice($arr_path, 2);
        } else {
            $arr_query = array();
        }
    } else {
        if ($path_info) {
            $controller = $path_info;
        } else {
            $controller = 'Index';
        }
        $method = 'index';
        $arr_query = array();
    }
    $controller = strtolower($controller);
    $method = strtolower($method);

    // ����ȫ�ֱ���
    $GLOBALS['controller'] = $controller;
    $GLOBALS['method'] = $method;
    
    // ���ؿ�����ִ��
    $controller = ucwords($controller);
    $methods = get_class_methods($controller);
    if ($methods) {
        if (in_array($method, $methods)) {
            $dispatch = new $controller();
            call_user_func_array(array($dispatch, $method), $arr_query);
        } else {
            show_error(998, ['title' => "The method <i>$method</i> does not exist."]);
        }
    } else {
        // ������������
        show_error(999, ['title' => "The controller <i>$controller</i> does not exist."]);
    }
}

// �Զ����ؿ����������ļ�
spl_autoload_register(function($class_name) {
    if (file_exists(APPPATH . 'controller/' . $class_name . '.php')) {
        include APPPATH . 'controller/' . $class_name . '.php';
    } elseif (file_exists(APPPATH . 'library/' . $class_name . '.php')) {
        include APPPATH . 'library/' . $class_name . '.php';
    } else {
        show_error(997, ['title' => "The class <i>$class_name</i> does not exist."]);
    }
});

// ���������쳣
set_error_handler('_error_handler');
set_exception_handler('_exception_handler');
register_shutdown_function('_shutdown_handler');

// ������������
require APPPATH . 'core/Controler.php';
