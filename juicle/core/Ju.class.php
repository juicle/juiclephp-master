<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Ju{
    // applications collections
    static private $_a = array();
    // components collections
    static private $_c = array();
    // config
    static private $_config = array();
    // autoload path
    static public $autoLoadPath;

    /**
     * init application.
     *
     * @return mixed
     */
    static public function init()
    {
        if (!AR_DEBUG) :
            error_reporting(0);
        endif;

        Ar::import(AR_CORE_PATH . 'alias.func.php');

        self::$autoLoadPath = array(
            AR_CORE_PATH,
            AR_FRAME_PATH,
            AR_COMP_PATH,
            AR_COMP_PATH . 'Db' . DS,
            AR_COMP_PATH . 'Url' . DS,
            AR_COMP_PATH . 'Format' . DS,
            AR_COMP_PATH . 'Validator' . DS,
            AR_COMP_PATH . 'Hash' . DS,
            AR_COMP_PATH . 'Rpc' . DS,
            AR_COMP_PATH . 'List' . DS,
            AR_COMP_PATH . 'Cache' . DS,
            AR_COMP_PATH . 'Tools' . DS,
            AR_COMP_PATH . 'Ext' . DS
        );
        if (AR_DEBUG && !AR_AS_CMD) :
            arComp('ext.out')->deBug('[START]');
        endif;
        // 子项目目录
        defined('AR_PUBLIC_CONFIG_PATH') or define('AR_PUBLIC_CONFIG_PATH', AR_ROOT_PATH . 'Conf' . DS);

        // 外部扩展库工具
        if (AR_OUTER_START) :
            Ar::c('url.skeleton')->generateIntoOther();
            $comonConfigFile = realpath(dirname(AR_MAN_PATH)) . DS . 'Conf' . DS . 'public.config.php';
            self::$_config = arComp('format.format')->arrayMergeRecursiveDistinct(
                Ar::import($comonConfigFile, true),
                Ar::import(AR_MAN_PATH . 'Conf' . DS . 'public.config.php')
            );
        elseif (AR_AS_WEB) :
            // 目录生成
            Ar::c('url.skeleton')->generate();
            // 公共配置
            if (!is_file(AR_PUBLIC_CONFIG_PATH . 'public.config.php') && !is_file(AR_PUBLIC_CONFIG_PATH . 'public.config.ini')) :
                echo 'config file not found : ' . AR_PUBLIC_CONFIG_PATH . 'public.config.php or ' . AR_PUBLIC_CONFIG_PATH . 'public.config.ini';
                exit;
            endif;
            self::setConfig('', Ar::import(AR_PUBLIC_CONFIG_PATH . 'public.config.php', true));
            // 加载ini
            $iniConfigFile = AR_PUBLIC_CONFIG_PATH . 'public.config.ini';
            $iniConfig = Ar::import($iniConfigFile, true);
            if (!empty($iniConfig)) :
                Ar::setConfig('', arComp('format.format')->arrayMergeRecursiveDistinct(Ar::getConfig(), $iniConfig));
            endif;

            // 引入新配置文件
            if (AR_PUBLIC_CONFIG_FILE && is_file(AR_PUBLIC_CONFIG_FILE)) :
                $otherConfig = include_once AR_PUBLIC_CONFIG_FILE;
                if (is_array($otherConfig)) :
                    Ar::setConfig('', arComp('format.format')->arrayMergeRecursiveDistinct($otherConfig, Ar::getConfig()));
                endif;
            endif;

            // 路由解析
            Ar::c('url.route')->parse();
            // 子项目目录
            defined('AR_APP_PATH') or define('AR_APP_PATH', AR_ROOT_PATH . (arCfg('requestRoute.a_m') ? arCfg('requestRoute.a_m') . DS : (AR_DEFAULT_APP_NAME ? AR_DEFAULT_APP_NAME . DS : '')));
            // app 配置目录
            defined('AR_APP_CONFIG_PATH') or define('AR_APP_CONFIG_PATH', AR_APP_PATH . 'Conf' . DS);
            // 模板目录
            defined('AR_APP_VIEW_PATH') or define('AR_APP_VIEW_PATH', AR_APP_PATH . 'View' . DS);
            // app 控制器目录
            defined('AR_APP_CONTROLLER_PATH') or define('AR_APP_CONTROLLER_PATH', AR_APP_PATH . 'Controller' . DS);
        // 命令行模式
        elseif (AR_AS_CMD) :
            // 目录生成
            Ar::c('url.skeleton')->generateCmdFile();
            self::$_config = Ar::import(AR_CMD_PATH . 'Conf' . DS . 'app.config.ini');
            self::$_config = arComp('format.format')->arrayMergeRecursiveDistinct(
                Ar::import(AR_CMD_PATH . 'Conf' . DS . 'app.config.ini'),
                Ar::import(AR_CMD_PATH . 'Conf' . DS . 'app.config.php', true)
            );
        endif;

        self::$_config = arComp('format.format')->arrayMergeRecursiveDistinct(
            Ar::import(AR_CONFIG_PATH . 'default.config.php', true),
            self::$_config
        );

        ArApp::run();

    }

    /**
     * set application.
     *
     * @param string $key key.
     * @param string $val key value.
     *
     * @return void
     */
    static public function setA($key, $val)
    {
        $classkey = strtolower($key);
        self::$_a[$classkey] = $val;

    }

    static public function getConfig($ckey = '', $defaultReturn = array()){
        $rt = array();

        if (empty($ckey)) :
            $rt = self::$_config;
        else :
            if (strpos($ckey, '.') === false) :
                if (isset(self::$_config[$ckey])) :
                    $rt = self::$_config[$ckey];
                else :
                    if (func_num_args() > 1) :
                        $rt = $defaultReturn;
                    else :
                        $rt = null;
                    endif;
                endif;
            else :
                $cE = explode('.', $ckey);
                $rt = self::$_config;
                // 0 判断
                while (($k = array_shift($cE)) || is_numeric($k)) :
                    if (!isset($rt[$k])) :
                        if (func_num_args() > 1) :
                            $rt = $defaultReturn;
                        else :
                            $rt = null;
                        endif;
                        break;
                    else :
                        $rt = $rt[$k];
                    endif;
                endwhile;
            endif;

        endif;

        return $rt;

    }

    /**
     * set config.
     *
     * @param string $ckey  key.
     * @param mixed  $value value.
     *
     * @return void
     */
    static public function setConfig($ckey = '', $value = array())
    {
        if (!empty($ckey)) :
            if (strpos($ckey, '.') === false) :
                self::$_config[$ckey] = $value;
            else :
                $cE = explode('.', $ckey);
                $rt = self::$_config;
                $nowArr = array();
                $length = count($cE);
                for ($i = $length - 1; $i >= 0; $i--) :
                    if ($i == $length - 1) :
                        $nowArr = array($cE[$i] => $value);
                    else :
                        $tem = $nowArr;
                        $nowArr = array();
                        $nowArr[$cE[$i]] = $tem;
                    endif;
                endfor;
                self::$_config = arComp('format.format')->arrayMergeRecursiveDistinct(
                    self::$_config,
                    $nowArr
                );
            endif;
        else :
            self::$_config = $value;
        endif;

    }

    /**
     * get application.
     *
     * @param string $akey key.
     *
     * @return mixed
     */
    static public function a($akey)
    {
        $akey = strtolower($akey);
        return isset(self::$_a[$akey]) ? self::$_a[$akey] : null;

    }

    /**
     * get component.
     *
     * @param string $cname component.
     *
     * @return mixed
     */
    static public function c($cname){
        $cKey = strtolower($cname);

        if (!isset(self::$_c[$cKey])){
            $config = self::getConfig('components.' . $cKey . '.config', array());
            self::setC($cKey, $config);
        }

        return self::$_c[$cKey];

    }

    /**
     * set component.
     *
     * @param string $component component name.
     * @param array  $config    component config.
     *
     * @return void
     */
    static public function setC($component, array $config = array())
    {
        $cKey = strtolower($component);

        if (isset(self::$_c[$cKey])) :
            return false;
        endif;

        $cArr = explode('.', $component);

        array_unshift($cArr, 'components');

        $cArr = array_map('ucfirst', $cArr);

        $className = 'Ar' . array_pop($cArr);

        $cArr[] = $className;

        $classFile = implode($cArr, '\\');

        self::$_c[$cKey] = call_user_func_array("$className::init", array($config, $className));

    }

    /**
     * autoload register.
     *
     * @param string $class class.
     *
     * @return mixed
     */
    static public function autoLoader($class)
    {
        $class = str_replace('\\', DS, $class);

        if (AR_OUTER_START) :
            $appModule = AR_MAN_PATH;
        else :
            $appModule = AR_ROOT_PATH . DS . arCfg('requestRoute.a_m', AR_DEFAULT_APP_NAME) . DS;
        endif;

        array_push(self::$autoLoadPath, $appModule);

        if (preg_match("#[A-Z]{1}[a-z0-9]+$#", $class, $match)) :
            $appEnginePath = $appModule . $match[0] . DS;
            $extPath = $appModule . 'Ext' . DS;
            // cmd mode
            $binPath = $appModule . 'Bin' . DS;
            $protocolPath = $appModule . 'Protocol' . DS;
            array_push(self::$autoLoadPath, $appEnginePath, $extPath, $binPath, $protocolPath);
        endif;
        self::$autoLoadPath = array_unique(self::$autoLoadPath);
        foreach (self::$autoLoadPath as $path) :
            $classFile = $path . $class . '.class.php';
            if (is_file($classFile)) :
                include_once $classFile;
                $rt = true;
                break;
            endif;
        endforeach;

        if (empty($rt)) :
            // 外部调用时其他框架还有其他处理 此处就忽略
            if (AR_AS_OUTER_FRAME || AR_OUTER_START) :
                return false;
            else :
                trigger_error('class : ' . $class . ' does not exist !', E_USER_ERROR);
                exit;
            endif;
        endif;

    }

    /**
     * set autoLoad path.
     *
     * @param string $path path.
     *
     * @return void
     */
    static public function importPath($path)
    {
        // array_push(self::$autoLoadPath, rtrim($path, DS) . DS);
        array_unshift(self::$autoLoadPath, rtrim($path, DS) . DS);

    }

    /**
     * import file or path.
     *
     * @param string  $path     import path.
     * @param boolean $allowTry allow test exist.
     *
     * @return mixed
     */
    static public function import($path, $allowTry = false)
    {
        static $holdFile = array();

        if (strpos($path, DS) === false) :
            $fileName = str_replace(array('c.', 'ext.', 'app.', '.'), array('Controller.', 'Extensions.', rtrim(AR_ROOT_PATH, DS) . '.', DS), $path) . '.class.php';
        else :
            $fileName = $path;
        endif;

        if (is_file($fileName)) :
            if (substr($fileName, (strrpos($fileName, '.') + 1)) == 'ini') :
                $config = parse_ini_file($fileName, true);
                if (empty($config)) :
                    $config = array();
                endif;
                return $config;
            else :
                $file = include_once $fileName;
                if ($file === true) :
                    return $holdFile[$fileName];
                else :
                    $holdFile[$fileName] = $file;
                    return $file;
                endif;
            endif;
        else :
            if ($allowTry) :
                return array();
            else :
                throw new ArException('import not found file :' . $fileName);
            endif;
        endif;

    }

    /**
     * exception handler.
     *
     * @param object $e Exception.
     *
     * @return void
     */
    static public function exceptionHandler($e)
    {
        if (get_class($e) === 'ArServiceException') :
            arComp('rpc.service')->response(array('error_code' => '1001', 'error_msg' => $e->getMessage()));
            exit;
        endif;

        if (AR_DEBUG && !AR_AS_CMD) :
            $msg = '<b style="color:#ec8186;">' . get_class($e) . '</b> : ' . $e->getMessage();
            if (arCfg('DEBUG_SHOW_TRACE')) :
                arComp('ext.out')->deBug($msg, 'TRACE');
            else :
                if (arCfg('DEBUG_SHOW_EXCEPTION')) :
                    arComp('ext.out')->deBug($msg, 'EXCEPTION');
                endif;
            endif;
        endif;

    }

    /**
     * error handler.
     *
     * @param string $errno   errno.
     * @param string $errstr  error msg.
     * @param string $errfile error file.
     * @param string $errline error line.
     *
     * @return mixed
     */
    static public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (AR_RUN_AS_SERVICE_HTTP) :
            arComp('rpc.service')->response(array('error_code' => '1011', 'error_msg' => $errstr));
            exit;
        endif;

        if (!AR_DEBUG || !(error_reporting() & $errno)) :
            return;
        endif;

        $errMsg = '';
        // 服务器级别错误
        $serverError = false;
        switch ($errno) {
        case E_USER_ERROR:
            $errMsg .= "<b style='color:red;'>ERROR</b> [$errno] $errstr<br />\n";
            $errMsg .= "  Fatal error on line $errline in file $errfile";
            $errMsg .= ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            $serverError = true;
            break;

        case E_USER_WARNING:
            $errMsg .= "<b style='color:#ec8186;'>WARNING</b> [$errno] $errstr<br />\n";
            $errMsg .= " on line $errline in file $errfile <br />\n";
            break;

        case E_USER_NOTICE:
        case E_NOTICE:
            $errMsg .= "<b style='color:#ec8186;'>NOTICE</b> [$errno] $errstr<br />\n";
            $errMsg .= " on line $errline in file $errfile <br />\n";
            break;

        default:
            $errMsg .= "<b style='color:#ec8186;'>Undefined error</b> : [$errno] $errstr";
            $errMsg .= " on line $errline in file $errfile <br />\n";
            break;
        }
        if ($errMsg) :
            if (arCfg('DEBUG_SHOW_TRACE')) :
                arComp('ext.out')->deBug($errMsg, 'TRACE');
            else :
                if (arCfg('DEBUG_SHOW_ERROR')) :
                    if ($serverError === true) :
                        arComp('ext.out')->deBug($errMsg, 'SERVER_ERROR');
                    else :
                        arComp('ext.out')->deBug($errMsg, 'ERROR');
                    endif;
                endif;
            endif;
        endif;

        return true;

    }

    /**
     * shutDown function.
     *
     * @return void
     */
    public static function shutDown()
    {
        if (AR_RUN_AS_SERVICE_HTTP) :
            return;
        endif;

        if (AR_DEBUG && !AR_AS_CMD) :
            if (arCfg('DEBUG_SHOW_EXCEPTION')) :
                arComp('ext.out')->deBug('', 'EXCEPTION', true);
            endif;

            if (arCfg('DEBUG_SHOW_ERROR')) :
                arComp('ext.out')->deBug('', 'ERROR', true);
                arComp('ext.out')->deBug('', 'SERVER_ERROR', true);
            endif;

            if (arCfg('DEBUG_SHOW_TRACE'))  :
                arComp('ext.out')->deBug('[SHUTDOWN]', 'TRACE', true);
            endif;

        endif;

    }

}
