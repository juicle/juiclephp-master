<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
return array(
    // path
    'PATH' => array(
        // Web服务器地址
        'APP_SERVER_PATH' => SERVER_PATH . Cfg('requestRoute.a_m') . '/',
        'PUBLIC' => SERVER_PATH . Cfg('requestRoute.a_m') . '/Public/',
        'GPUBLIC' => SERVER_PATH . 'Public/',
        // 兼容以前
        'CACHE' => ROOT_PATH . Cfg('requestRoute.a_m') . DS . 'Cache' . DS,
        'LOG' => ROOT_PATH . Cfg('requestRoute.a_m') . DS . 'Log' . DS,
        'VIEW' => ROOT_PATH . Cfg('requestRoute.a_m') . DS . 'View' . DS,
        'UPLOAD' => ROOT_PATH . Cfg('requestRoute.a_m') . DS . 'Upload' . DS,
        'EXT' => ROOT_PATH . Cfg('requestRoute.a_m') . DS . 'Ext' . DS,
    ),

    // 相关路径
    'DIR' => array(
        'CACHE' => ROOT_PATH . Cfg('requestRoute.a_m') . DS . 'Cache' . DS,
        'LOG' => ROOT_PATH . Cfg('requestRoute.a_m') . DS . 'Log' . DS,
        'VIEW' => ROOT_PATH . Cfg('requestRoute.a_m') . DS . 'View' . DS,
        'UPLOAD' => ROOT_PATH . Cfg('requestRoute.a_m') . DS . 'Upload' . DS,
        'EXT' => ROOT_PATH . Cfg('requestRoute.a_m') . DS . 'Ext' . DS,
        // 片段目录
        'SEG' => PUBLIC_CONFIG_PATH . 'Seg' . DS,
    ),

    // url
    'URL_MODE' => 'PATH',
    // 全局url 贪婪模式
    'URL_GREEDY' => false,

    // debug
    'DEBUG_SHOW_TRACE' => true,
    'DEBUG_SHOW_ERROR' => true,
    'DEBUG_SHOW_EXCEPTION' => true,

    // 是否调试信息到日志文件
    'DEBUG_LOG' => false,

    // 默认的模板后缀
    'TPL_SUFFIX' => 'php',

    // 路由规则
    'URL_ROUTE_RULES' => array(
        // 'index/index' => array(
        //     'mode' => 'testindex:a:/:b:', // url will be localhost/testindex123/456   a will be 123 b 456
        // ),
    ),

);
