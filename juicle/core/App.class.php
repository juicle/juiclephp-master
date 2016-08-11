<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class App{

    static public function run(){
        if (IS_DEBUG && !AS_CMD){
            Comp('ext.out')->deBug('[APP_RUN]');
        }

        self::_initComponents(Ju::getConfig('components', array()));
        // 外部启用直接不执行app
        if (OUTER_START){
            return;
        }
        if (RUN_AS_SERVICE_HTTP){
            $app = self::_createWebApplication('ArApplicationServiceHttp');
            $app->start();
        }else if (AS_CMD){
            $app = self::_createWebApplication('ArApplicationCmd');
            $app->start();
        }else if (AS_WEB){
            $app = self::_createWebApplication('ApplicationWeb');
            $app->start();
        }

    }

    static private function _initComponents(array $config){
        foreach ($config as $driver => $component){
            if (!is_array($component)){
                continue;
            }
            // 默认的加载规则 lazy=false才会预加载
            if (empty($component['lazy']) || $component['lazy'] == true){
                continue;
            }
            foreach ($component as $engine => $cfg){
                if (!empty($cfg['lazy']) && $cfg['lazy'] == true || $engine == 'lazy'){
                    continue;
                }

                $configC = !empty($cfg['config']) ? $cfg['config'] : array();

                Ju::setC($driver . '.' . $engine, $configC);
            }
        }

    }


    static private function _createWebApplication($class){
        $classkey = strtolower($class);

        if (!Ju::a($classkey)){
            Ju::setA($classkey, new $class);
        }

        return Ju::a($classkey);

    }

}
