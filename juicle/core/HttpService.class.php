<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class HttpService{
   
    protected function response($data = ''){
        return Comp('rpc.service')->response($data);

    }

    public function init(){

    }

}
