<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class ApplicationWeb extends Application{

    public $route = array();

    public function start(){
        if (IS_DEBUG && !AS_CMD){
            Comp('ext.out')->deBug('[APP_WEB_START]');
        }
        if (AUTO_START_SESSION && ini_get('session.auto_start') == 0){
            session_start();
        }

        $this->processRequest();

    }


    public function processRequest(){
        $this->runController(Ju::getConfig('requestRoute'));
    }

    
    public function runController($route){
        if (IS_DEBUG && !AS_CMD){
            Comp('ext.out')->deBug('[CONTROLLER_RUN]');
        }

        Ju::setConfig('requestRoute', $route);

        if (empty($route['a_c'])){
            $c = 'Index';
        }else{
            $c = ucfirst($route['a_c']);
        }

        $this->route['a_c'] = $c;
        $class = $c . 'Controller';

        if (IS_DEBUG && !AS_CMD){
            Comp('ext.out')->deBug('|CONTROLLER_EXEC:'. $class .'|');
        }

        if (class_exists($class)){
            $this->_c = new $class;
            $this->_c->init();
            $action = ($a = empty($route['a_a']) ? DEFAULT_ACTION : $route['a_a']) . 'Action';
            $this->route['a_a'] = $a;
            if (is_callable(array($this->_c, $action))){
                try {
                    if (IS_DEBUG && !AS_CMD){
                        Comp('ext.out')->deBug('|ACTION_RUN:' . $action . '|');
                    }
                    $this->_c->$action();
                } catch (BaseException $e) {
                    if (!AS_OUTER_FRAME){
                        throw new BaseException($e->getMessage());
                    }
                }
            }else{
                if (!AS_OUTER_FRAME){
                    throw new BaseException('Action ' . $action . ' not found');
                }
            }
        }

    }

}
