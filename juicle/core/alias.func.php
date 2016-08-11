<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
function Comp($name = ''){
    return Ju::c($name);
}

function Cfg($name = '', $default = 'NOT_RGI'){
    if ($default === 'NOT_RGI'){
        return Ju::getConfig($name);
    }else{
        return Ju::getConfig($name, $default);
   }

}



function U($name = '', $params = array(), $urlMode = 'NOT_INIT'){
    return Comp('url.route')->createUrl($name, $params, $urlMode);
}

function Module($name = ''){
    static $moduleList = array();
    $module = $name . 'Module';
    if (!array_key_exists($module, $moduleList)) :
        arComp('ext.out')->deBug('|MODULE_INIT:' . $module .'|');
        $moduleList[$module] = new $module;
        if (is_callable(array($moduleList[$module], 'initModule'))) :
            call_user_func_array(array($moduleList[$module], 'initModule'), array());
        endif;
    endif;
    if (DEBUG && !AS_CMD) :
        arComp('ext.out')->deBug('|MODULE_EXEC:' . $module .'|');
    endif;
    return $moduleList[$module];

}


/**
 * filter $_GET.
 *
 * @param string $key     get key.
 * @param mixed  $default return value.
 *
 * @return mixed
 */
function arGet($key = '', $default = null)
{
    $getUrlParamsArray = arComp('url.route')->parseGetUrlIntoArray();
    $ret = array();

    if (empty($key)) :
        $ret = $getUrlParamsArray;
    else :
        if (!isset($getUrlParamsArray[$key])) :
            $ret = null;
        else :
            $ret = $getUrlParamsArray[$key];
        endif;
    endif;

    $ret = arComp('format.format')->addslashes($ret);
    if (is_numeric($ret) && $ret < 2147483647 && strlen($ret) == 1) :
        $ret = (int)$ret;
    elseif (empty($ret)) :
        $ret = $default;
    endif;

    return arComp('format.format')->trim($ret);

}

/**
 * filter $_POST.
 *
 * @param string $key     post key.
 * @param mixed  $default return value.
 *
 * @return mixed
 */
function Post($key = '', $default = null)
{
    $ret = array();

    if (empty($key)) :
        $ret = $_POST;
    else :
        if (!isset($_POST[$key])) :
            $ret = $default;
        else :
            $ret = $_POST[$key];
        endif;
    endif;

    return arComp('format.format')->addslashes(arComp('format.format')->trim($ret));

}

/**
 * filter $_REQUEST 有缓冲.
 *
 * @param string $key      post      key.
 * @param mixed  $default  return    value.
 * @param array  $addArray add merge array.
 *
 * @return mixed
 */
function arRequest($key = '', $default = null, $addArray = array())
{
    static $request = array();
    if (empty($request) || !empty($addArray)) :
        if (!is_array($addArray)) :
            $addArray = array();
        endif;
        $getArr = arGet('', array());
        $postArr = arPost('', array());
        $request = array_merge($getArr, $postArr, $addArray);
        $request = arComp('format.format')->addslashes(arComp('format.format')->trim($request));
    endif;

    if ($key) :
        if (array_key_exists($key, $request)) :
            $ret = $request[$key];
        else :
            $ret = $default;
        endif;
    else :
        $ret = $request;
    endif;

    return $ret;

}

/**
 * load other module.
 *
 * @param string $module name.
 *
 * @return mixed
 */
function arLm($module)
{
    return Ar::importPath(ROOT_PATH . str_replace('.', DS, $module));

}

/**
 * echo for default
 *
 * @param string $echo    echo.
 * @param string $default default out.
 * @param string $key     key.
 *
 * @return void
 */
function arEcho($echo = '', $default = '', $key = '')
{
    if (is_array($default)) :
        $index = (int)$echo;
        if (arComp('validator.validator')->checkMutiArray($default)) :
            $echo = !empty($default[$index]) && !empty($default[$index][$key]) ? $default[$index][$key] : '';
        else :
            $echo = empty($default[$index]) ? '' : $default[$index];
        endif;
    else :
        if (empty($echo)) :
            $echo = $default;
        endif;
    endif;

    echo $echo;

}

/**
 * Html segment.
 *
 * @param string $seg html 片段 通过 $this->assign 分配.
 *
 * @return void
 */
function arSeg($segment)
{
    if (!is_array($segment)) :
        throw new ArException("segment must be an array");
    endif;

    if (empty($segment['segKey'])) :
        $keyBundle = array_keys($segment);
        $segKey = $keyBundle[0];
    else :
        $segKey = $segment['segKey'];
    endif;
    extract($segment);
    $segFile = arCfg('DIR.SEG') . str_replace('/', DS, $segKey) . '.seg';
    if (!is_file($segFile)) :
        $segFile .= '.php';
        if (!is_file($segFile)) :
            throw new ArException("segment file " . $segFile . ' not found');
        endif;
    endif;
    include $segFile;

}
