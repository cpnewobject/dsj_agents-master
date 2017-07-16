<?php
return array(
    
    //url的配置
    'URL_CASE_INSENSITIVE'=>true,
    'URL_MODEL' => '2',
    
    // 应用配置
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common'),
    'MODULE_ALLOW_LIST'  => array('Home'),
    
    //数据库配置
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => '192.168.20.67', // 服务器地址
    'DB_NAME'   => 'dashijie', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '123456', // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => 'dsj_', // 数据库表前缀 
    'DB_CHARSET'=> 'utf8', // 字符集
    'DB_DEBUG'  =>  TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
    /* 接口*/
    'DEFAULT_TIMEZONE' => 'PRC',
);