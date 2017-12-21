<?php
/**
 * 模板引擎类
 *
 * 进行模板文件编译，编译规则可进一步改进
 * 配置项在config文件中定义
 *
 * @package        Hooloo Framework
 * @author         Passerby
 * @version        1.2
 * @release        2017.10.31
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Template
{
    private $tpl_html;      // 视图源文件名
    private $tpl_compile;   // 编译文件名
    private $tpl_update;    // 编译时间
    
    public function __construct($html = '')
    {
        if ($html) {
            // 源文件
            if (file_exists($html)) {
                $this->tpl_html = $html;
            } else {
                exit('视图文件不存在：' . $html);
            }
            //编译文件
            $this->tpl_compile = TPL_COMPILE_PATH . '/' . md5($html) . '.php';
            if (file_exists($this->tpl_compile) && DEVELOPMENT_ENVIRONMENT == false) {
                $this->tpl_update = filemtime($this->tpl_compile);
            } else {
                $this->tpl_update = 0;
            }
        } else {
            exit('视图文件不存在：null');
        }
    }
    
    // 显示页面
    public function display($data = [])
    {
        // 编译文件是否已过期
        if (filemtime($this->tpl_html) > $this->tpl_update) {
            // 重新编译文件
            $this->compile();
        }
        // 分配变量
        if ($data) {
            extract($data);
        }
        // 加载编译文件
        include($this->tpl_compile);
    }
    
    // 编译文件
    private function compile()
    {
        // 处理界定符
        $s = TPL_LEFT_SEPERATOR;
        $lt = '/';
        for ($i = 0; $i < strlen($s); $i++) {
            $lt .= '\\' . $s[$i];
        }
        $lt .= '\s*';
        
        $s = TPL_RIGHT_SEPERATOR;
        $gt = '\s*';
        for ($i = 0; $i < strlen($s); $i++) {
            $gt .= '\\' . $s[$i];
        }
        $gt .= '/i';
        
        // 读取源文件
        $a = file_get_contents($this->tpl_html);
        
        /**
         * 编译过程
         * 需按次序逐步进行
         *
         * 1.处理包含文件
         * {include file="dir/file.html"} => 导入文件
         */
        $p = 'include\s+file\s*=\s*[\"\']?(.+?)[\"\']?';
        while (preg_match_all($lt . $p . $gt, $a, $res, PREG_SET_ORDER)) {
            foreach ($res as $v) {
                $html = APPPATH . 'view/' . $v[1];
                if (file_exists($html)) {
                    $inc_file = file_get_contents($html);
                    $a = str_replace($v[0], $inc_file, $a);
                } else {
                    exit('包含文件不存在：' . $v[1]);
                }
            }
        }
        
        /**
         * 2.格式化变量
         * 最大支持三维数组，数组写法可用：$a['b'], $a["b"], $a[b], $a.b => $a['b']
         */
        $l = '/(\{|\(|\[|\=|\s)';
        $vn = '(\$[A-z_]\w*)';
        $vi = '(?:\[[\'\"]?(\w+)[\'\"]?\])';
        $vi2 = '(?:\.(\w+))';
        $r = '(\,|\s|\=|\)|\})/';
        $a = preg_replace($l . $vn . $vi . $vi . $vi . $r, "\\1\\2['\\3']['\\4']['\\5']\\6", $a);
        $a = preg_replace($l . $vn . $vi . $vi . $r, "\\1\\2['\\3']['\\4']\\5", $a);
        $a = preg_replace($l . $vn . $vi . $r, "\\1\\2['\\3']\\4", $a);
        $a = preg_replace($l . $vn . $vi2 . $vi2 . $vi2 . $r, "\\1\\2['\\3']['\\4']['\\5']\\6", $a);
        $a = preg_replace($l . $vn . $vi2 . $vi2 . $r, "\\1\\2['\\3']['\\4']\\5", $a);
        $a = preg_replace($l . $vn . $vi2 . $r, "\\1\\2['\\3']\\4", $a);
        $a = preg_replace($l . $vn . $r, "\\1\\2\\3", $a);
        
        /**
         * 3.格式化循环语句
         * {foreach $aa as $k to $v} => <?php foreach ($aa as $k => $v) { ?>
         * {foreach $aa as $v} => <?php foreach ($aa as $v) { ?>
         * {/foreach} => <?php } ?>
         * {for $i = 1 to 10 step 2} => <?php for ($i = 1; $i <= 10; $i += 2) { ?>
         * {/for} => <?php } ?>
         */
        $vn = '(\$[A-z_]\w*(?:\[(?:(?:\'\w+\')|(?:\$[A-z_]\w*))\]){0,3})';
        $p = 'foreach[\s\(]+' . $vn . '\s+as\s+' . $vn . '\s+to\s+' . $vn . '[\s\)]*';
        $a = preg_replace($lt . $p . $gt, '<?php foreach (\\1 as \\2 => \\3) { ?>', $a);
        $p = 'foreach[\s\(]+' . $vn . '\s+as\s+' . $vn . '[\s\)]*';
        $a = preg_replace($lt . $p . $gt, '<?php foreach (\\1 as \\2) { ?>', $a);
        $p = 'for[\s\(]+' . $vn . '\s*\=\s*(\S+)\s*to\s*(\S+)\s*step\s*\-\s?(\S+)[\s\)]*';
        $a = preg_replace($lt . $p . $gt, '<?php for (\\1 = \\2; \\1 >= \\3; \\1 -= \\4) { ?>', $a);
        $p = 'for[\s\(]+' . $vn . '\s*\=\s*(\S+)\s*to\s*(\S+)\s*step\s*(\S+)[\s\)]*';
        $a = preg_replace($lt . $p . $gt, '<?php for (\\1 = \\2; \\1 <= \\3; \\1 += \\4) { ?>', $a);
        $p = 'for[\s\(]+' . $vn . '\s*\=\s*(\S+)\s*to\s*(\S+)[\s\)]*';
        $a = preg_replace($lt . $p . $gt, '<?php for (\\1 = \\2; \\1 <= \\3; \\1++) { ?>', $a);
        $a = preg_replace($lt . '\/(foreach|for|if)' . $gt, '<?php } ?>', $a);
        
        /**
         * 4.格式化判断语句
         * {if 1 == $a} => <?php if (1 == $a) { ?>
         * {elseif 2 == $a} => <?php } elseif (2 == $a) { ?>
         * {else} => <?php } else { ?>
         * {/if} => <?php } ?>
         */
        $a = preg_replace($lt . 'if\s+([^\}]+)' . $gt, '<?php if (\\1) { ?>', $a);
        $a = preg_replace($lt . 'else\s*if\s+([^\}]+)' . $gt, '<?php } elseif (\\1) { ?>', $a);
        $a = preg_replace($lt . 'else' . $gt, '<?php } else { ?>', $a);
        // 封闭标签放在循环里处理
        
        /**
         * 5.格式化变量输出
         * 请不要使用<?= ?>短标签，后面会进行php标签合并
         */
        $a = preg_replace($lt . $vn . $gt, '<?php echo \\1; ?>', $a);
        /**
         * 6.格式化注释
         * 注释语法 {// comments...} 或 {/* comments... * /}，{# comments... #}
         * 编译时删除注释
         */
        $a = preg_replace($lt . '\/\/.*' . $gt, '', $a);
        $a = preg_replace($lt . '\#.*?\#' . $gt, '', $a);
        $a = preg_replace($lt . '\/\*[^\/]+?\*\/' . $gt, '', $a);
        
        // 格式化其他输出
        $a = preg_replace($lt . '([^\}]+)' . $gt, '<?php echo \\1; ?>', $a);
        
        // 压缩空格，开发模式不处理以便于调试
        if (DEVELOPMENT_ENVIRONMENT !== true) {
            $a = preg_replace('/\s+/', ' ', $a);
            $a = str_replace('> <', '><', $a);
        }
        
        // 删除js多行注释
        $a = preg_replace('/\/\*(.+?)\*\//', '', $a);
        // 删除html注释
        $a = preg_replace('/<!--(.+?)\/\/-->/', '', $a);
        
        // 合并php标签
        $a = preg_replace('/;\s?;/', ';', $a);
        $a = str_replace('?><?php ', ' ', $a);
        
        // 判断编译目录是否存在
        if (! file_exists(TPL_COMPILE_PATH)) {
            mkdir(TPL_COMPILE_PATH);
        }
        
        // 写入编译文件
        file_put_contents($this->tpl_compile, $a);
    }
}
