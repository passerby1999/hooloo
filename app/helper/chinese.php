<?php
/**
 * 汉字处理函数
 *
 * @package        Hooloo framework
 * @author         Passerby
 * @version        1.2
 * @release        2017.11.1
 */
if (! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * utf8字符串替换函数
 * @param     string    $str        要转换的字符串
 * @param     string    $from         
 * @param     string    $to         
 * @return    string    $result     返回结果集
 */
function mb_strtr($str, $from, $to)
{
    preg_match_all('/./u', $from, $keys);
    preg_match_all('/./u', $to, $values);
    $arr = array_combine($keys[0], $values[0]);
    return strtr($str, $arr);
}
