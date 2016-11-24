<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
abstract class Cache extends Component{

    
    abstract function get($key);

    
    abstract function set($key, $value);

    
    abstract function del($key);

    
    abstract function flush();

    
    protected function generateUniqueKey($keyName){
        return md5($keyName);
    }

    
    protected function encrypt($data){
        return serialize($data);
    }

    protected function decrypt($data){
        return unserialize($data);
    }

}
