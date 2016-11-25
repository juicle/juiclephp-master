<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Controller{
    
    protected $assign = array();
    
    protected $layOutFile = 'NOT_INIT';

    
    public function init(){

    }

   
    public function __call($name, $params){
        $mName = empty($params[0]) ? Cfg('requestRoute.a_c') : $params[0];
        if ($name == 'module'){
            return Module($mName);
        }elseif ($name == 'model') {
            $m = $mName . 'Model';
            return Model::model($m);
        }else {
            throw new BaseException("class do not have a method $name");
        }

    }

    
    public function assign(array $vals){
        foreach ($vals as $key => $val) {
            if (is_array($val) && isset($this->assign[$key]) && is_array($this->assign[$key])){
                $this->assign[$key] = array_merge($this->assign[$key], $val);
            }else {
                $this->assign[$key] = $val;
            }
        }

    }

   
    public function show($ckey = '', $defaultReturn = '', $show = true){
        $rt = array();
        if (empty($ckey)) :
            $rt = $this->assign;
        else :
            if (strpos($ckey, '.') === false) :
                if (isset($this->assign[$ckey])) :
                    $rt = $this->assign[$ckey];
                endif;
            else :
                $cE = explode('.', $ckey);
                $rt = $this->assign;
                while ($k = array_shift($cE)) :
                    if (empty($rt[$k])) :
                        $rt = $defaultReturn;
                        break;
                    else :
                        $rt = $rt[$k];
                    endif;
                endwhile;
            endif;
        endif;
        if ($show) :
            echo $rt;
        else :
            return $rt;
        endif;

    }

    
    protected function display($view = '', $fetch = false){
        $headerFile = '';
        $footerFile = '';

        if ($this->layOutFile === 'NOT_INIT'){
            $headerFile = APP_VIEW_PATH . 'Layout' . DS . 'header' . '.' . Cfg('TPL_SUFFIX');
            $footerFile = APP_VIEW_PATH . 'Layout' . DS . 'footer' . '.' . Cfg('TPL_SUFFIX');
        }else if ($this->layOutFile){
            $headerFile = $this->layOutFile . '_header' . '.' . Cfg('TPL_SUFFIX');
            $footerFile = $this->layOutFile . '_footer' . '.' . Cfg('TPL_SUFFIX');
        }

        // 加载头
        if ($headerFile) :
            if (is_file($headerFile)) :
                $this->fetch($headerFile);
            else :
                if ($this->layOutFile !== 'NOT_INIT') :
                    throw new Exception("not fount layout header file : " . $headerFile, '2000');
                endif;
            endif;
        endif;

        // 加载模板
        $this->fetch($view, $fetch);

        // 加载尾部
        if ($footerFile) :
            if (is_file($footerFile)) :
                $this->fetch($footerFile);

            else :
                if ($this->layOutFile !== 'NOT_INIT') :
                    throw new Exception("not fount layout footer file : " . $footerFile, '2000');
                endif;
            endif;
        endif;

        if ($fetch === false) :
            // 加载退出
            exit;
        endif;

    }

    
    protected function fetch($view = '', $fetch = false)
    {
        if (is_file($view)) :
            $viewFile = $view;
        else :
            $viewPath = '';
            $viewBasePath = Cfg('PATH.VIEW');
            $overRide = false;
            $absolute = false;

            if (strpos($view, '@') === 0) :
                $overRide = true;
                $view = ltrim($view, '@');
            endif;

            $r = Ju::a('ApplicationWeb')->route;

            if (empty($view)) :
                $viewPath .= $r['a_c'] . DS . $r['a_a'];
            elseif(strpos($view, '/') !== false) :
                if (substr($view, 0, 1) == '/') :
                    $absolute = true;
                    $viewPath .= str_replace('/', DS, ltrim($view, '/'));
                else :
                    $viewPath .= $r['a_c'] . DS  . str_replace('/', DS, ltrim($view, '/'));
                endif;
                if (substr($view, -1) == '/') :
                    $viewPath .= $r['a_a'];
                endif;
            else :
                $viewPath .= $r['a_c'] . DS . $view;
            endif;

            $currentC = $tempC = $r['a_c'] . 'Controller';

            $preFix = '';

            if (!$absolute) :
                while ($cP = get_parent_class($tempC)) :
                    if (!in_array(substr($cP, 0, -10), array('Ju', 'Base'))) :
                        $preFix = substr($cP, 0, -10) . DS . $preFix;
                        if (!$overRide && method_exists($cP, $r['a_a'] . 'Action')) :
                            $viewPath = str_replace(substr($tempC, 0, -10) . DS, '', $viewPath);
                        endif;
                        $tempC = $cP;
                    else :
                        break;
                    endif;
                endwhile;
            endif;
            $viewFile = $viewBasePath . $preFix . $viewPath . '.' . Cfg('TPL_SUFFIX');
        endif;

        if (is_file($viewFile)) :
            extract($this->assign);
            if ($fetch === true) :
                ob_start();
                include $viewFile;
                $fetchStr = ob_get_contents();
                ob_end_clean();
                return $fetchStr;
            else :
                include $viewFile;
            endif;
        else :
            throw new Exception('view : ' . $viewFile . ' not found');
        endif;

    }

    
    public function redirect($r = '', $show = '', $time = '0'){
        return Comp('url.route')->redirect($r, $show, $time, Cfg('SEG_REDIRECT_DEFAULT', 'default'));

    }

    
    public function redirectSuccess($r = '', $show = '', $time = '1'){
        return Comp('url.route')->redirect($r, '操作成功! ' . $show, $time, Cfg('SEG_REDIRECT_SUCCESS', 'success'));

    }

    
    public function redirectError($r = '', $show = '' , $time = '4'){
        return Comp('url.route')->redirect($r, '操作失败! ' . $show, $time, Cfg('SEG_REDIRECT_ERROR', 'error'));

    }

    
    public function showJsonSuccess($msg = ' '){
        $this->showJson(array('ret_msg' => $msg, 'ret_code' => '1000', 'success' => "1"));

    }

    
    public function showJsonError($msg = ' ', $code = '1001'){
        $this->showJson(array('ret_msg' => $msg, 'ret_code' => $code, 'error_msg' => $msg, 'success' => "0"));

    }

    
    public function showJson($data = array(), array $options = array()){
        return Comp('ext.out')->json($data, $options);

    }

    
    public function ifLogin(){
        return !!Comp('list.session')->get('uid');

    }

    
    public function logOut(){
        Comp('list.session')->set('uid', null);

    }

    
    public function runController($module){
        $route = explode('/', $module);

        $requestRoute = array(
                'a_m' => Cfg('requestRoute.a_m'),
                'a_c' => $route[0],
                'a_a' => $route[1],
            );

        Ju::a('ApplicationWeb')->runController($requestRoute);

    }

    
    public function setLayoutFile($layoutFileName = ''){
        if ($layoutFileName){
            if (!is_file($layoutFileName)){
                $layoutFileName = APP_VIEW_PATH . 'Layout' . DS . $layoutFileName;
            }
        }

        $this->layOutFile = $layoutFileName;

    }

}
