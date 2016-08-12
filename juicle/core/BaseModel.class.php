<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class BaseModel{
    
    public $nowModel = '';

    public $tableName = '';

    private static $_models = array();

    
    static public function model($class = __CLASS__){
        $key = strtolower($class);

        if (!isset(self::$_models[$key])){
            if (IS_DEBUG && !AS_CMD){
                Comp('ext.out')->deBug('|MODEL_INIT:' . $class . '|');
            }

            // not instance model
            if (strlen($class) <= 5 || substr($class, '-5') !== 'Model'){
                $obj = new self;
                $obj->tableName = strtolower($class);
            }else{
                $obj = new $class;
            }

            $obj->nowModel = $class;

            if (IS_DEBUG && !AS_CMD){
                Comp('ext.out')->deBug('|MODEL_START:' . $class . '|');
            }

            self::$_models[$key] = $obj;
        }else{
            if (IS_DEBUG && !AS_CMD){
                Comp('ext.out')->deBug('|MODEL_RESTART:' . $class . '|');
            }
        }

        return self::$_models[$key];

    }

   
    public function upload($field, $type = 'img'){
        $upFile = Comp('ext.upload')->upload($field, '', $type);

        if (!$upFile){
            Comp('list.log')->set($this->nowModel, Comp('ext.upload')->errorMsg());
            return false;
        }else{
            return $upFile;
        }

    }

   
    public function getDb($dbType = 'mysql', $dbString = 'default', $read = true){
        if ($read){
            return Comp('db.' . $dbType)->table($this->tableName)->read($dbString)->setSource($this->nowModel);
        }else{
            return Comp('db.' . $dbType)->table($this->tableName)->write($dbString)->setSource($this->nowModel);
        }

    }

    
    public function rules(){
        return array();

    }

    
    public function updateCheck(array $data = array()){
        $rules = $this->rules();

        foreach ($rules as $key => $rule){
            if (empty($rules[2]) || $rules[2] != 'update' ){
                unset($rules[$key]);
            }
        }

        return $this->insertCheck($data, $rules);

    }

    
    public function insertCheck(array $data = array(), array $rules = array()):bool{
        $rules = empty($rules) ? $this->rules() : $rules;

        $r = Comp('validator.validator')->checkDataByRules($data, $rules);

        if (empty($r)){
            return true;
        }


        $errorMsg = '';

        foreach ($r as $errorR){
            $errorMsg .= $errorR[1] . "\n";
        }

        Comp('list.log')->set($this->nowModel, $errorMsg);

        return false;

    }

   
    public function formatData($data){
        return $data;

    }

}

