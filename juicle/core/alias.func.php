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
    if (!array_key_exists($module, $moduleList)){
        Comp('ext.out')->deBug('|MODULE_INIT:' . $module .'|');
        $moduleList[$module] = new $module;
        if (is_callable(array($moduleList[$module], 'initModule'))){
            call_user_func_array(array($moduleList[$module], 'initModule'), array());
        }
    }
    if (IS_DEBUG && !AS_CMD){
        Comp('ext.out')->deBug('|MODULE_EXEC:' . $module .'|');
    }
    return $moduleList[$module];

}

function Get($key = '', $default = null){
    $getUrlParamsArray = Comp('url.route')->parseGetUrlIntoArray();
    $ret = array();

    if (empty($key)){
        $ret = $getUrlParamsArray;
    }else{
        if (!isset($getUrlParamsArray[$key])){
            $ret = null;
        }else{
            $ret = $getUrlParamsArray[$key];
        }
    }

    $ret = Comp('format.format')->addslashes($ret);
    if (is_numeric($ret) && $ret < 2147483647 && strlen($ret) == 1){
        $ret = (int)$ret;
    }else if (empty($ret)){
        $ret = $default;
    }

    return Comp('format.format')->trim($ret);

}

function Post($key = '', $default = null){
    $ret = array();

    if (empty($key)){
        $ret = $_POST;
    }else{
        if (!isset($_POST[$key])){
            $ret = $default;
        }else{
            $ret = $_POST[$key];
        }
    }

    return Comp('format.format')->addslashes(Comp('format.format')->trim($ret));

}


function Request($key = '', $default = null, $addArray = array()){
    static $request = array();
    if (empty($request) || !empty($addArray)){
        if (!is_array($addArray)){
            $addArray = array();
        }
        $getArr = Get('', array());
        $postArr = Post('', array());
        $request = array_merge($getArr, $postArr, $addArray);
        $request = Comp('format.format')->addslashes(Comp('format.format')->trim($request));
    }

    if ($key){
        if (array_key_exists($key, $request)){
            $ret = $request[$key];
        }else{
            $ret = $default;
        }
    }else{
        $ret = $request;
    }

    return $ret;

}


function Lm($module){
    return Ju::importPath(ROOT_PATH . str_replace('.', DS, $module));
}


function Output($echo = '', $default = '', $key = ''){
    if (is_array($default)){
        $index = (int)$echo;
        if (Comp('validator.validator')->checkMutiArray($default)){
            $echo = !empty($default[$index]) && !empty($default[$index][$key]) ? $default[$index][$key] : '';
        }else{
            $echo = empty($default[$index]) ? '' : $default[$index];
        }
    }else{
        if (empty($echo)){
            $echo = $default;
        }
    }

    echo $echo;

}


function Seg($segment){
    if (!is_array($segment)){
        throw new BaseException("segment must be an array");
    }

    if (empty($segment['segKey'])){
        $keyBundle = array_keys($segment);
        $segKey = $keyBundle[0];
    }else{
        $segKey = $segment['segKey'];
    }
    extract($segment);
    $segFile = arCfg('DIR.SEG') . str_replace('/', DS, $segKey) . '.seg';
    if (!is_file($segFile)){
        $segFile .= '.php';
        if (!is_file($segFile)){
            throw new ArException("segment file " . $segFile . ' not found');
        }
    }
    include $segFile;

}
