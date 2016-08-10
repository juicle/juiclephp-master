<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Route extends Component
{

    public function serverPath($dir, $showServerName = false):string{
        $dir = str_replace(DS, '/', $dir);
        $path = dirname($_SERVER['SCRIPT_FILENAME']);
        $position = strpos($dir, $path);
        if ($position !== false) {
            $dir = SERVER_PATH . trim(str_replace($path, '', $dir), '/');
        }
        return ($showServerName ? $this->serverName() : '') . $dir;

    }

    public function pathToDir($path):string{
        if (strpos($path, '/') === 0) {
            $dir = rtrim(realpath($_SERVER['DOCUMENT_ROOT']), DS) . DS;
            $path = trim($path, '/');
            $path = str_replace('/', DS, $path);
            $dir = $dir . $path;
        }else {
            $path = str_replace('/', DS, $path);
            $dir = ROOT_PATH . $path;
        }

        return $dir;

    }


    public function host($scriptName = false):string{
        $host = $this->serverName() . '/' . trim(str_replace(array('/', '\\', DS), '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        $host = rtrim($host, '/');
        if ($scriptName) {
            $host .= '/' . basename($_SERVER['SCRIPT_NAME']);
        }
        return $host;

    }


    public function serverName():string{
        return 'http://' . $_SERVER['HTTP_HOST'];
    }

    public function parseUrlForRules($url):string{
        $foundMode = false;
        $baseTrimUrl = substr($url, strlen(SERVER_PATH));
        $absolutePath = ltrim($baseTrimUrl, '/');
        if (strpos($absolutePath, '?') !== false){
            $absolutePath = substr($absolutePath, 0, strpos($absolutePath, '?'));
        }
        if (strpos($absolutePath, '/') === false){
            $virtualModule = $absolutePath;
        }else{
            $virtualModule = substr($absolutePath, 0, strpos($absolutePath, '/'));
        }
        if (!in_array($virtualModule, arCfg('moduleLists'))){
            $virtualModule = DEFAULT_APP_NAME;
        }
        // 预加载config
        $appConfigFile = ROOT_PATH . $virtualModule . DS . 'Conf' . DS . 'app.config.php';
        // ini
        $iniConfigFile = ROOT_PATH . $virtualModule . DS . 'Conf' . DS . 'app.config.ini';
        // 项目配置
        $appConfig = Ar::import($appConfigFile, true);
        $iniConfig = Ar::import($iniConfigFile, true);

        if (!empty($iniConfig)) :
            Ar::setConfig('', arComp('format.format')->arrayMergeRecursiveDistinct(Ar::getConfig(), $iniConfig));
        endif;

        if (!empty($appConfig)) :
            Ar::setConfig('', arComp('format.format')->arrayMergeRecursiveDistinct(Ar::getConfig(), $appConfig));
        endif;
        $urlRouteRules = arCfg('URL_ROUTE_RULES');
        if (is_array($urlRouteRules)) :
            foreach ($urlRouteRules as $key => $rules) :
                if (is_array($rules['mode'])) :
                    foreach ($rules['mode'] as $mode) :
                        if ($mode === $absolutePath) :
                            $url = AR_SERVER_PATH . $key;
                            $foundMode = true;
                            break 2;
                        endif;
                        preg_match_all('|:(.*):|U', $mode, $match);
                        if (!empty($match[1])) :
                            $mode = preg_replace('|(:.*:)|U', '([a-zA-z0-9%]+)', $mode);
                            $urlRegRules = '|' . $mode . '|';
                            if (preg_match_all($urlRegRules, $url, $matchRules)) :
                                $lengthOfVariable = count($match[1]);
                                for ($i = 0; $i < $lengthOfVariable; $i++) :
                                    $rulesKey = $i + 1;
                                    $_GET[$match[1][$i]] = $matchRules[$rulesKey][0];
                                endfor;
                                $url = preg_replace('|(.*)' . $mode . '(.*)|', "$1" . $key . "$" . ($lengthOfVariable + 2), $url);
                                break;
                            else :
                                continue;
                            endif;
                        endif;
                    endforeach;
                else :
                    throw new ArException('URL_ROUTE_RULES : "' . $key . '" mode should be an Array', 1006);
                endif;
            endforeach;
        endif;
        return $url;

    }

    /**
     * parse string.
     *
     * @return mixed
     */
    public function parse()
    {
        $requestUrl = $this->parseUrlForRules($_SERVER['REQUEST_URI']);
        $phpSelf = $_SERVER['SCRIPT_NAME'];
        if (strpos($requestUrl, $phpSelf) !== false) :
            $requestUrl = str_replace($phpSelf, '', $requestUrl);
        endif;
        if (($pos = strpos($requestUrl, '?')) !== false) :
            $queryStr = substr($requestUrl, $pos + 1);
            $requestUrl = substr($requestUrl, 0, $pos);
        endif;
        if (($root = dirname($phpSelf)) != '/' && $root != '\\') :
            $requestUrl = preg_replace("#^{$root}#", '', $requestUrl);
        endif;
        $requestUrl = trim($requestUrl, '/');
        $pathArr = explode('/', $requestUrl);
        $temp = array_shift($pathArr);
        $m = in_array($temp, Ar::getConfig('moduleLists', array())) ? $temp : AR_DEFAULT_APP_NAME;
        $c = in_array($temp, Ar::getConfig('moduleLists', array())) ? array_shift($pathArr) : $temp;
        $a = array_shift($pathArr);
        while ($gkey = array_shift($pathArr)) :
            $_GET[$gkey] = array_shift($pathArr);
        endwhile;
        if (!empty($queryStr)) :
            parse_str($queryStr, $query);
            foreach ($_GET as $gkey => $gval) :
                if (array_key_exists($gkey, $query) && empty($query[$gkey])) :
                    unset($query[$gkey]);
                endif;
            endforeach;
            $_GET = array_merge($_GET, $query);
        endif;
        if (arGet('a_m')) :
            $m = arGet('a_m');
        endif;
        if (arGet('a_c')) :
            $c = arGet('a_c');
        endif;
        if (arGet('a_a')) :
            $a = arGet('a_a');
        endif;
        // 解析子域名 hostname
        $a_h = '';
        if (strpos($_SERVER['HTTP_HOST'], '.') !== false) :
            $serverHostArray = explode('.', $_SERVER['HTTP_HOST']);
            if (count($serverHostArray) == 3) :
                $a_h = $serverHostArray[0];
            endif;
        endif;
        $requestRoute = array('a_h' => $a_h, 'a_m' => $m, 'a_c' => empty($c) ? AR_DEFAULT_CONTROLLER : $c, 'a_a' => empty($a) ? AR_DEFAULT_ACTION : $a);
        Ar::setConfig('requestRoute', $requestRoute);
        return $requestRoute;

    }

    /**
     * generate url get parame.
     *
     * @return array
     */
    public function parseGetUrlIntoArray()
    {
        static $staticMark = array(
            'firstParse' => true,
            'getUrlParamArray' => array(),
        );
        if ($staticMark['firstParse']) :
            $parseUrl = parse_url($_SERVER['REQUEST_URI']);

            if (empty($parseUrl['query'])) :

            else :
                parse_str($parseUrl['query'], $query);
                foreach ($_GET as $gkey => $gval) :
                    if (array_key_exists($gkey, $query) && empty($query[$gkey])) :
                        unset($query[$gkey]);
                    endif;
                endforeach;
                $staticMark['getUrlParamArray'] = $query;
            endif;
            $staticMark['getUrlParamArray'] = array_merge($_GET, $staticMark['getUrlParamArray']);
            $staticMark['firstParse'] = false;
        endif;
        return $staticMark['getUrlParamArray'];

    }

    /**
     * url manage.
     *
     * @param string  $urlKey      route key.
     * @param boolean $params  url get param.
     * @param string  $urlMode url mode.
     *
     * @return string
     */
    public function createUrl($urlKey = '', $params = array(), $urlMode = 'NOT_INIT')
    {
        // 路由url
        $url = $urlKey;
        // 路由规则
        $urlRouteRules = arCfg('URL_ROUTE_RULES');
        $defaultModule = arCfg('requestRoute.a_m') == AR_DEFAULT_APP_NAME ? '' : arCfg('requestRoute.a_m');
        if ($urlMode === 'NOT_INIT') :
            $urlMode = arCfg('URL_MODE', 'PATH');
        endif;
        $prefix = rtrim(AR_SERVER_PATH . $defaultModule, '/');
        $urlParam = arCfg('requestRoute');
        $urlParam['a_m'] = $defaultModule;

        if (isset($params['greedyUrl']) && $params['greedyUrl'] === false) :
            // do nothing
        else :
            if ((isset($params['greedyUrl']) && $params['greedyUrl'] === true) || arCfg('URL_GREEDY') === true) :
                unset($params['greedyUrl']);
                unset($_GET['a_m']);
                unset($_GET['a_c']);
                unset($_GET['a_a']);
                // 合并参数
                if (is_array(arGet())) :
                    $getArr = arGet();
                    unset($getArr['a_m']);
                    unset($getArr['a_c']);
                    unset($getArr['a_a']);
                    $params = array_merge($getArr, $params);
                endif;
            endif;
        endif;
        // 跳转回来
        if (isset($params['ar_back']) && $params['ar_back'] === true) :
            unset($params['ar_back']);
            arComp('list.session')->set('ar_back_url', $_SERVER['REQUEST_URI']);
        endif;
        if (empty($url)) :
            if ($urlMode == 'PATH') :
                $controller = arCfg('requestRoute.a_c');
                $action = arCfg('requestRoute.a_a');
                $url .= '/' . $controller . '/' . $action;
                // 后续匹配
                $urlKey = trim($url, '/');
                $url = $prefix . $url;
            endif;
        else :
            // url
            if (strpos($url, 'http') === 0) :
                $urlArr = parse_url($url);
                $reBuildUrlArr = $params;
                if (!empty($urlArr['query'])) :
                    parse_str($urlArr['query'], $urlStrArr);
                    $reBuildUrlArr = array_filter(array_merge($params, $urlStrArr));
                    $baseUrl = substr($url, 0, strpos($url, '?'));
                else :
                    $baseUrl = rtrim($url, '?');
                endif;
                $reBuildUrl = $baseUrl . '?' . http_build_query($reBuildUrlArr);
                return $reBuildUrl;
            elseif (strpos($url, '/') === false) :
                if ($urlMode != 'PATH') :
                    $urlParam['a_a'] = $url;
                else :
                    $url = $prefix . '/' . arCfg('requestRoute.a_c') . '/' . $url;
                endif;
            elseif (strpos($url, '/') === 0) :
                if ($urlMode != 'PATH') :
                    $eP = explode('/', ltrim($url, '/'));
                    $urlParam['a_m'] = $eP[0];
                    $urlParam['a_c'] = isset($eP[1]) ? $eP[1] : null;
                    $urlParam['a_a'] = isset($eP[2]) ? $eP[2] : null;
                else :
                    $url = ltrim($url, '/');
                    $url = AR_SERVER_PATH . $url;
                endif;
            else :
                if ($urlMode != 'PATH') :
                    $eP = explode('/', $url);
                    $urlParam['a_c'] = $eP[0];
                    $urlParam['a_a'] = $eP[1];
                else :
                    $url = $prefix . '/' . $url;
                endif;
            endif;

        endif;

        if ($urlMode != 'PATH') :
            $urlParam = array_filter(array_merge($urlParam, $params));
        endif;

        // 初始化config时
        if (empty($urlMode)) :
            $urlMode = 'PATH';
        endif;
        switch ($urlMode) {

        case 'PATH' :
            if (strpos($urlKey, '/') === false) :
                $urlKey = arCfg('requestRoute.a_c') . '/' . $urlKey;
            endif;
            // 路由解析
            if (array_key_exists($urlKey, $urlRouteRules)) :
                // 检测数组时候
                $findMode = false;
                if (is_array($urlRouteRules[$urlKey]['mode'])) :
                    foreach ($urlRouteRules[$urlKey]['mode'] as $mode) :
                        // 已寻找到模式
                        if ($findMode) :
                            break;
                        endif;
                        if (!preg_match('|:(.*):|', $mode)) :
                            $url = str_replace($urlKey, $mode, $url);
                            $findMode = true;
                            break;
                        else :
                            $tempUrl = str_replace($urlKey, $mode, $url);
                            preg_match_all('|:(.*):|U', $tempUrl, $match);
                            // 匹配的变量
                            if (!empty($match[1])) :
                                $sizeMatch = count($match[1]);
                                for ($i = 0; $i < $sizeMatch; $i++) :
                                    $variable = $match[1][$i];
                                    if (array_key_exists($variable, $params)) :
                                        $tempUrl = str_replace(':' . $variable . ':', $params[$variable], $tempUrl);
                                        if ($i == ($sizeMatch - 1)) :
                                            $findMode = true;
                                            $url = $tempUrl;
                                            foreach ($match[1] as $variable) :
                                                unset($params[$variable]);
                                            endforeach;
                                            break;
                                        endif;
                                    else :
                                        break;
                                    endif;
                                endfor;
                            endif;
                        endif;
                    endforeach;
                else :
                    throw new ArException('URL_ROUTE_RULES : "' . $urlKey . '" mode should be an Array', 1006);
                endif;
            endif;
            foreach ($params as $pkey => $pvalue) :
                if (!$pvalue && !is_numeric($pvalue)) :
                    continue;
                endif;
                $url .= '/' . $pkey . '/' . $pvalue;
            endforeach;
            break;
        case 'QUERY' :
            $url = arComp('url.route')->host() . '?' . http_build_query($urlParam);
            break;
        case 'FULL' :
            $url = arComp('url.route')->host(true) . '?' . http_build_query($urlParam);
            break;
        }
        return $url;

    }

    public function redirect($r = '', $show = '', $time = '0', $seg = ''){
        $show = trim($show);
        $show = preg_replace("/\n/", ' ', $show);
        if(is_string($r)){
            $url = '';
            if (empty($r)){
                $urlTemp = arComp('list.session')->get('ar_back_url');
                if ($urlTemp) {
                    $url = $urlTemp;
                    arComp('list.session')->set('ar_back_url', null);
                }
            }else{
                if ($r == 'ar_up') {
                    if (!empty($_SERVER['HTTP_REFERER'])){
                        $url = $_SERVER['HTTP_REFERER'];
                    }
                }else if(strpos($r, 'http') !== false) {
                    $url = $r;
                }else{
                    $url = arU($r);
                }
            }
        }else{
            $route = empty($r[0]) ? '' : $r[0];
            $param = empty($r[1]) ? array() : $r[1];

            $url = arComp('url.route')->createUrl($route, $param);
        }
        // search seg if found then render
        $redirectUrl = <<<str
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Refresh" content="$time;URL=$url" />
</head>
<body>
$show<a href="$url">立即跳转</a>
</body>
</html>
str;
        if ($seg) :
            // filename
            $seg = 'Redirect/' . $seg;
            try {
                arSeg(array('segKey' => $seg, 'url' => $url, 'show' => $show, 'time' => $time));
                exit;
            } catch (ArException $e) {

            }
        endif;
        echo $redirectUrl;
        exit;

    }

}
