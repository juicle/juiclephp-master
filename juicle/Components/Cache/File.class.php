<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class File extends Cache{
    
    static public function init($config = array(), $class = __CLASS__){
        $obj = parent::init($config, $class);

        $obj->cachePath = empty($obj->config['cachePath']) ? arCfg('PATH.CACHE') : $obj->config['cachePath'];

        if(!is_dir($obj->cachePath)) :
            mkdir($obj->cachePath, 0777, true);
        endif;

        return $obj;

    }

    public function cacheFile($key){
        return $this->cachePath . $this->generateUniqueKey($key) . '.cache';
    }

    
    public function get($key){
        $cacheFile = $this->cacheFile($key);

        if (is_file($cacheFile)) :
            if ($this->checkExpire($cacheFile)) :
                $data = null;
                $this->del($key);
                $data = null;
            else :
                $data = $this->decrypt(file_get_contents($cacheFile, false, null, 10));
            endif;
        else :
            $data = null;
        endif;

        return $data;

    }

    
    public function set($key, $value, $expire = 0){
        if ($expire == 0) :
            $timeExpire = '0000000000';
        else :
            $timeExpire = time() + $expire;
        endif;

        return file_put_contents($this->cacheFile($key), $timeExpire . $this->encrypt($value));

    }

    public function del($key){
        $cacheFile = $this->cacheFile($key);

        if (is_file($cacheFile)) :
            unlink($cacheFile);
        endif;

        return true;

    }

    public function checkExpire($file){
        $timeExpire = file_get_contents($file, false, null, 0, 10);

        return $timeExpire == 0 ? false : ($timeExpire < time());

    }

    
    public function flush($force = false, $dir = ''){
        $cachePath = $dir ? $dir : $this->cachePath;
        if (is_dir($cachePath)) :
            $source = opendir($cachePath);
            while ($file = readdir($source)) :
                $file = $cachePath . $file;
                if (is_file($file)) :
                    if ($force || $this->checkExpire($file)) :
                        unlink($file);
                    endif;
                endif;
            endwhile;
            closedir($source);
        endif;

    }

    public function flushAll($force = true, $module = array()){
        if (empty($module)) :
            $module = arCfg('moduleLists');
        endif;
        foreach ($module as $dir) :
            $dir = AR_ROOT_PATH . $dir . DS . 'Cache' . DS;
            $this->flush($force, $dir);
        endforeach;

    }

}
