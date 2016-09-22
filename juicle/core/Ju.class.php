<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Ju{
   
    static private $_a = array();
    
    static private $_c = array();
    
    static private $_config = array();
    
    static public $autoLoadPath;

    
    static public function init(){
        if (!IS_DEBUG){
            error_reporting(0);
        }

        self::import(CORE_PATH . 'alias.func.php');

        self::$autoLoadPath = array(
            CORE_PATH,
            FRAME_PATH,
            COMP_PATH,
            COMP_PATH . 'Db' . DS,
            COMP_PATH . 'Url' . DS,
            COMP_PATH . 'Format' . DS,
            COMP_PATH . 'Validator' . DS,
            COMP_PATH . 'Hash' . DS,
            COMP_PATH . 'Rpc' . DS,
            COMP_PATH . 'List' . DS,
            COMP_PATH . 'Cache' . DS,
            COMP_PATH . 'Tools' . DS,
            COMP_PATH . 'Ext' . DS
        );
        if (IS_DEBUG && !AS_CMD) :
            Comp('ext.out')->deBug('[START]');
        endif;
        // 子项目目录
        defined('PUBLIC_CONFIG_PATH') or define('PUBLIC_CONFIG_PATH', ROOT_PATH . 'Conf' . DS);

        // 外部扩展库工具
        if (OUTER_START) :
            self::c('url.skeleton')->generateIntoOther();
            $comonConfigFile = realpath(dirname(MAN_PATH)) . DS . 'Conf' . DS . 'public.config.php';
            self::$_config = Comp('format.format')->arrayMergeRecursiveDistinct(
                Ar::import($comonConfigFile, true),
                Ar::import(MAN_PATH . 'Conf' . DS . 'public.config.php')
            );
        elseif (AS_WEB) :
            // 目录生成
            self::c('url.skeleton')->generate();
            // 公共配置
            if (!is_file(PUBLIC_CONFIG_PATH . 'public.config.php') && !is_file(PUBLIC_CONFIG_PATH . 'public.config.ini')) :
                echo 'config file not found : ' . PUBLIC_CONFIG_PATH . 'public.config.php or ' . PUBLIC_CONFIG_PATH . 'public.config.ini';
                exit;
            endif;
            self::setConfig('', Ju::import(PUBLIC_CONFIG_PATH . 'public.config.php', true));
            // 加载ini
            $iniConfigFile = PUBLIC_CONFIG_PATH . 'public.config.ini';
            $iniConfig = Ju::import($iniConfigFile, true);
            if (!empty($iniConfig)) :
                Ju::setConfig('', Comp('format.format')->arrayMergeRecursiveDistinct(Ju::getConfig(), $iniConfig));
            endif;

            // 引入新配置文件
            if (PUBLIC_CONFIG_FILE && is_file(PUBLIC_CONFIG_FILE)) :
                $otherConfig = include_once PUBLIC_CONFIG_FILE;
                if (is_array($otherConfig)) :
                    Ju::setConfig('', Comp('format.format')->arrayMergeRecursiveDistinct($otherConfig, Ju::getConfig()));
                endif;
            endif;

            // 路由解析
            Ju::c('url.route')->parse();
            // 子项目目录
            defined('APP_PATH') or define('APP_PATH', ROOT_PATH . (Cfg('requestRoute.a_m') ? Cfg('requestRoute.a_m') . DS : (DEFAULT_APP_NAME ? DEFAULT_APP_NAME . DS : '')));
            // app 配置目录
            defined('APP_CONFIG_PATH') or define('APP_CONFIG_PATH', APP_PATH . 'Conf' . DS);
            // 模板目录
            defined('APP_VIEW_PATH') or define('APP_VIEW_PATH', APP_PATH . 'View' . DS);
            // app 控制器目录
            defined('APP_CONTROLLER_PATH') or define('APP_CONTROLLER_PATH', APP_PATH . 'Controller' . DS);
        // 命令行模式
        elseif (AS_CMD) :
            // 目录生成
            Ju::c('url.skeleton')->generateCmdFile();
            self::$_config = Ju::import(CMD_PATH . 'Conf' . DS . 'app.config.ini');
            self::$_config = Comp('format.format')->arrayMergeRecursiveDistinct(
                Ju::import(CMD_PATH . 'Conf' . DS . 'app.config.ini'),
                Ju::import(CMD_PATH . 'Conf' . DS . 'app.config.php', true)
            );
        endif;

        self::$_config = Comp('format.format')->arrayMergeRecursiveDistinct(
            Ju::import(CONFIG_PATH . 'default.config.php', true),
            self::$_config
        );

        App::run();

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
                self::$_config = Comp('format.format')->arrayMergeRecursiveDistinct(
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

    
    static public function setC($component, array $config = array()){
        $cKey = strtolower($component); 

        if (isset(self::$_c[$cKey])) :
            return false;
        endif;

        $cArr = explode('.', $component);

        array_unshift($cArr, 'components');

        $cArr = array_map('ucfirst', $cArr);

        $className = array_pop($cArr);

        $cArr[] = $className;

        $classFile = implode($cArr, '\\');

        self::$_c[$cKey] = call_user_func_array("$className::init", array($config, $className));

    }

    
    static public function autoLoader($class){
        $class = str_replace('\\', DS, $class);

        if (OUTER_START) :
            $appModule = MAN_PATH;
        else :
            $appModule = ROOT_PATH . DS . Cfg('requestRoute.a_m', DEFAULT_APP_NAME) . DS;
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
            if (AS_OUTER_FRAME || OUTER_START) :
                return false;
            else :
                trigger_error('class : ' . $class . ' does not exist !', E_USER_ERROR);
                exit;
            endif;
        endif;

    }

    
    static public function importPath($path)
    {
        // array_push(self::$autoLoadPath, rtrim($path, DS) . DS);
        array_unshift(self::$autoLoadPath, rtrim($path, DS) . DS);

    }

    
    static public function import($path, $allowTry = false){
        static $holdFile = array();

        if (strpos($path, DS) === false) :
            $fileName = str_replace(array('c.', 'ext.', 'app.', '.'), array('Controller.', 'Extensions.', rtrim(ROOT_PATH, DS) . '.', DS), $path) . '.class.php';
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
            Comp('rpc.service')->response(array('error_code' => '1001', 'error_msg' => $e->getMessage()));
            exit;
        endif;

        if (DEBUG && !AS_CMD) :
            $msg = '<b style="color:#ec8186;">' . get_class($e) . '</b> : ' . $e->getMessage();
            if (Cfg('DEBUG_SHOW_TRACE')) :
                Comp('ext.out')->deBug($msg, 'TRACE');
            else :
                if (Cfg('DEBUG_SHOW_EXCEPTION')) :
                    Comp('ext.out')->deBug($msg, 'EXCEPTION');
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
        if (RUN_AS_SERVICE_HTTP) :
            Comp('rpc.service')->response(array('error_code' => '1011', 'error_msg' => $errstr));
            exit;
        endif;

        if (!IS_DEBUG || !(error_reporting() & $errno)) :
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
            if (Cfg('DEBUG_SHOW_TRACE')) :
                Comp('ext.out')->deBug($errMsg, 'TRACE');
            else :
                if (Cfg('DEBUG_SHOW_ERROR')) :
                    if ($serverError === true) :
                        Comp('ext.out')->deBug($errMsg, 'SERVER_ERROR');
                    else :
                        Comp('ext.out')->deBug($errMsg, 'ERROR');
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
        if (RUN_AS_SERVICE_HTTP) :
            return;
        endif;

        if (IS_DEBUG && !AS_CMD) :
            if (Cfg('DEBUG_SHOW_EXCEPTION')) :
                Comp('ext.out')->deBug('', 'EXCEPTION', true);
            endif;

            if (Cfg('DEBUG_SHOW_ERROR')) :
                Comp('ext.out')->deBug('', 'ERROR', true);
                Comp('ext.out')->deBug('', 'SERVER_ERROR', true);
            endif;

            if (Cfg('DEBUG_SHOW_TRACE'))  :
                Comp('ext.out')->deBug('[SHUTDOWN]', 'TRACE', true);
            endif;

        endif;

    }

}
