# hooloo 是一个简单高效的PHP框架。

1、安装说明
    
    1）请使用 PHP 5.6 或更高版本，数据库请使用 MySQL 5.5 或更高版本。
    
    2）index.php 必须位于网站根目录。

    目录结构：

    /app/.        应用程序目录（子目录名不可修改）
        /config        配置文件
        /controler    控制器
        /core        框架核心文件
        /helper        辅助函数
        /library        公共类文件
        /model        模型（私有类）
        /view        视图文件
    /data/.        临时数据目录（可自定义）
        /cache        缓存日志
        /logs        错误日志
        /runtime    模板编译文件
        /session    会话信息

2、默认控制器：

    Index

3、默认方法：

    index

4、关键设置：

    /index.php文件：
        DEVELOPMENT_ENVIRONMENT 开发环境调试模式，正式环境下设为false；

    /app/config/config.php文件：
        SERVER_NAME 服务器域名，需与正式开放域名一致；

    /app/config/database.php文件：
        数据库连接信息配置；

    /app/config/routes.php文件：
        路由配置；
