<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Proxy extends Api
{
    // mime 类型
    static $MIMETYPEMAP = array(
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/tiff' => 'tif',
    );

    // domain
    protected $domainInfo = array();
    // default mime type
    protected $mimeType = 'text/html';
    // file suffix
    protected $fileSuffix;

    /**
     * remote call.
     *
     * @param string $url call url.
     *
     * @return mixed
     */
    public function remoteCall($url, $params = array(), $method = '')
    {
        if ($method) :
            $this->method = $method;
        else :
            $this->method = empty($this->config['method']) ? 'get' : $this->config['method'];
        endif;
        $this->parse($url);
        $init = curl_init($url);
        $options = array(
            CURLOPT_RETURNTRANSFER => 1,

            CURLOPT_AUTOREFERER => 1,

            CURLOPT_RETURNTRANSFER => 1,

            CURLOPT_HTTPHEADER => array(
                    'User-Agent' => 'User-Agent:Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36',
                    'Host' => $this->domainInfo['host'],
                    'Referer' => $this->domainInfo['referer'],
            )
        );

        if ($this->method == 'post') :
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $params;
        endif;

        curl_setopt_array($init, $options);
        $rtStr = curl_exec($init);
        $info = curl_getinfo($init);
        if (!empty($info['content_type'])) :
            $this->mimeType = $info['content_type'];
            if (array_key_exists($this->mimeType, self::$MIMETYPEMAP)) :
                $this->fileSuffix = self::$MIMETYPEMAP[$this->mimeType];
            endif;
        endif;
        curl_close($init);
        return $rtStr;

    }

    /**
     * call api
     *
     * @param string $url   url.
     * @param boolean $show display.
     *
     * @return void
     */
    public function callApi($url, $show = true)
    {
        $source = $this->remoteCall($url);
        // 是否显示
        if ($show === true) :
            header('Content-Type:' . $this->mimeType);
            echo $source;
        else :
            if (strstr($show, '.') === false && $this->fileSuffix) :
                $show .= '.' . $this->fileSuffix;
            endif;
            // 保存文件
            file_put_contents($show, $source);
            return $show;
        endif;

    }

    /**
     * parse return data.
     *
     * @param string $url url resource.
     *
     * @return void
     */
    protected function parse($url)
    {
        $uInfo = parse_url($url);
        if (empty($uInfo['host']) || empty($uInfo['scheme'])) :
            throw new ArException('url ' . $url . ' may have a valid host');
        endif;
        $this->domainInfo['host'] = $uInfo['host'];
        $this->domainInfo['referer'] = $uInfo['scheme'] . '://'. $uInfo['host'];

    }

}
