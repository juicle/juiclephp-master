<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Source extends Api
{
    /**
     * parse return data.
     *
     * @param string $parseStr not parsed string.
     *
     * @return string
     */
    protected function parse($parseStr)
    {
        return $parseStr;

    }

    /**
     * getApi.
     *
     * @param string $api    api.
     * @param mixed  $params param.
     *
     * @return string
     */
    protected function getApi($api, $params)
    {
        if (strpos($api, 'http://') === false) :
            if (arCfg('URL_MODE') == 'PATH') :
                $prefix = rtrim(empty($this->config['remotePrefix']) ? arComp('url.route')->ServerName() : $this->config['remotePrefix'], '/');
            endif;
        else :
            $prefix = '';
        endif;
        if (!empty($params['curlOptions'])) :
            $this->curlOptions = $params['curlOptions'];
            unset($params['curlOptions']);
        endif;

        switch ($this->method) {
        case 'get' :
            if (empty($this->config['remotePrefix'])) :
                $prefix .= arU($api, $params);
            else :
                $prefix .= '/' . ltrim($api, '/');
                if (!empty($params)) :
                    $prefix .= '?' . http_build_query($params);
                endif;
            endif;
            break;
        case 'post' :
            $prefix .= empty($this->config['remotePrefix']) ? arU($api) : ('/' . ltrim($api, '/'));
            break;
        }

        $url = trim($prefix, '/');

        return $this->remoteCall($url, $params, $this->method);

    }

    /**
     * call api.
     *
     * @param string $api    api.
     * @param mixed  $params parames.
     * @param string $method http method.
     *
     * @return mixed
     */
    public function callApi($api, $params = array(), $method = '')
    {
        if ($method) :
            $this->method = $method;
        else :
            $this->method = empty($this->config['method']) ? 'get' : $this->config['method'];
        endif;

        $result = $this->getApi($api, $params);

        return $this->parse($result);

    }

}
