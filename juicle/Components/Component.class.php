<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */

class Component{

    protected $config = array();

    static public function init($config = array(), $class = __CLASS__){
        $obj = new $class;
        if ($config){
            $obj->config = $config;
        }
        return $obj;
    }

    public function setConfig($config = array()){
        $this->config = $config;
    }

    public function getConfig($ckey = ''):string{
        $rt = '';
        if ($ckey){
            if (!empty($this->config[$ckey])){
                $rt = $this->config[$ckey];
            }
        }else{
            $rt = $this->config;
        }
        return $rt;
    }

}
