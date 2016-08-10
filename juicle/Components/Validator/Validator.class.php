<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */

class Validator extends Component{

    public function checkNumber($obj):bool{
        return is_numeric($obj);
    }

    public function checkMutiArray($obj):bool{
        $rt = true;
        if(is_array($obj)){
            foreach ($obj as $arr){
                if (!is_array($arr)){
                    $rt = false;
                    break;
                }
            }
        }else{
            $rt = false;
        }
    }

    public function checkUrl($url):int{
        return preg_match("#^(http)#", $url);
    }

    public function checkArrayKeyEqual(array $arri, array $arro):bool{
        $lengthi = count($arri);
        $lengtho = count($arro);

        $rt = true;
        
        if($lengthi !== $lengtho){
            $rt = false;
        }else{
            foreach ($arri as $ikey => $ivalue){
                if (!array_key_exists($ikey, $arro)){
                    $rt = false;
                    break;
                }
            }
        }
        return $rt;
    }

    public function checkAjax():bool{
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
            return true;
        }else{
            return false;
        }
    }

    public function checkEmpty($obj):bool{
        return empty($obj);

    }

    public function checkDataByRules(array $data, array $rules):array{
        foreach($rules as $k => $rule){
            if (array_key_exists($k, $data)){
                switch ($rule[0]) {
                case 'number' :
                    if ($this->checkNumber($data[$k])) :
                        unset($rules[$k]);
                    endif;
                    break;
                case 'required' :
                    if (!$this->checkEmpty($data[$k])) :
                        unset($rules[$k]);
                    endif;
                    break;
                default :
                    if (!$this->checkEmpty($data[$k])) :
                        unset($rules[$k]);
                    endif;
                    break;
                }
            } 
        }
        return $rules;
    }

}
