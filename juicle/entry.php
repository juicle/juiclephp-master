<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
// 启动时间
defined('START_TIME') or define('START_TIME', microtime(true));
// 开启调试 是
defined('IS_DEBUG') or define('IS_DEBUG', true);
// 外部启动 否 默认管理目录Man
defined('OUTER_START') or define('OUTER_START', false);
// 自启动session
defined('AUTO_START_SESSION') or define('AUTO_START_SESSION', true);
// 作为外部框架加载 可嵌入其他框架
defined('AS_OUTER_FRAME') or define('AS_OUTER_FRAME', false);
// 内部实现http webservice 多套 程序互调接口
defined('RUN_AS_SERVICE_HTTP') or define('RUN_AS_SERVICE_HTTP', false);
// 实现 cmd socket 编程
defined('AS_CMD') or define('AS_CMD', false);
// web application 默认方式
defined('AS_WEB') or define('AS_WEB', true);
// app名 main
defined('DEFAULT_APP_NAME') or define('DEFAULT_APP_NAME', 'main');
// 默认的控制器名
defined('DEFAULT_CONTROLLER') or define('DEFAULT_CONTROLLER', 'Index');
// 默认的Action
defined('DEFAULT_ACTION') or define('DEFAULT_ACTION', 'index');
// 目录分割符号
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
// 框架目录
defined('FRAME_PATH') or define('FRAME_PATH', dirname(__FILE__) . DS);
// 项目根目录
defined('ROOT_PATH') or define('ROOT_PATH', realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . DS);
// 核心目录
defined('CORE_PATH') or define('CORE_PATH', FRAME_PATH . 'Core' . DS);
// 配置目录
defined('CONFIG_PATH') or define('CONFIG_PATH', FRAME_PATH . 'Conf' . DS);
// 扩展目录
defined('EXT_PATH') or define('EXT_PATH', FRAME_PATH . 'Extensions' . DS);
// 模块目录
defined('COMP_PATH') or define('COMP_PATH', FRAME_PATH . 'Components' . DS);
// 服务地址
defined('SERVER_PATH') or define('SERVER_PATH', ($dir = dirname($_SERVER['SCRIPT_NAME'])) == DS ? '/' : str_replace(DS, '/', $dir) . '/');
// 默认配置文件
defined('PUBLIC_CONFIG_FILE') or define('PUBLIC_CONFIG_FILE', '');
//是否开启代码严格模式
defined('STRICT_TYPES') or define('STRICT_TYPES', 1);

require_once CORE_PATH . 'Ju.class.php';

spl_autoload_register('Ju::autoLoader');

if(OUTER_START){
    defined('MAN_NAME') or define('MAN_NAME', 'man');
    defined('MAN_PATH') or define('MAN_PATH', ROOT_PATH . MAN_NAME . DS);
}else if(AS_CMD){
    defined('CMD_PATH') or define('CMD_PATH', ROOT_PATH . DEFAULT_APP_NAME . DS);
}else{
    set_exception_handler('Ju::exceptionHandler');
    set_error_handler('Ju::errorHandler');
    register_shutdown_function('Ju::shutDown');
}

//装载
Ju::init();
