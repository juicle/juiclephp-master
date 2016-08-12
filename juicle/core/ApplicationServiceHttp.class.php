<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class ApplicationServiceHttp extends ApplicationService{
   
    public function start(){
        $data = $this->parseHttpServiceHanlder();
        return $this->runService($data);

    }

    
    public function parseHttpServiceHanlder(){
        if ($ws = Post('ws')){
            if (!$ws = Comp('rpc.api')->decrypt($ws)){
                throw new ServiceException('ws query format incorrect error');
            }

            if (empty($ws['class']) || empty($ws['method']) || !isset($ws['param'])){
                throw new ServiceException('ws query param missing error');
            }

            return array(
                    'class' => $ws['class'],
                    'method' => $ws['method'],
                    'param' => $ws['param'],
                );

        }else{
            throw new ServiceException('ws query ws info missing error');
        }

    }

    
    protected function runService($ws = array()){
        $service = $ws['class'] . 'Service';
        $method = $ws['method'] . 'Worker';
        $param = $ws['param'];

        try {

            $serviceHolder = new $service;
            $serviceHolder->init();

            if (!is_callable(array($serviceHolder, $method))){
                throw new ServiceException('ws service do not hava a method ' . $method);
            }
            return call_user_func_array(array($serviceHolder, $method), $param);
        } catch(Exception $e) {
            throw new ServiceException($e->getMessage());
        }
    }

}
