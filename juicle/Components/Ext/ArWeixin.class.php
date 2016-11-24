<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.Component.List
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.org
 */

/**
 * Core.Component.Weixin
 *
 * default hash comment :
 *
 * config
 *'ext' => array(
    'lazy' => true,
    'weixin' => array(
        'config' => array(
            'APPID' => 'wx37b8059cb2bf453e',
            'APPSECRET' => 'a732c465fb149c4937e012b60081f687',
            'menu' => array(
                'button' => array(
                    array(
                        'name' => 'test1',
                        'type' => 'click',
                        'key' => 'test1',
                    ),
                    array(
                        'name' => 'test2',
                        'type' => 'click',
                        'key' => 'test2',
                    ),
                    array(
                        'name' => 'test3',
                        'type' => 'click',
                        'key' => 'test3',
                    ),
                ),
            ),
        ),
    ),
),
 * <code>
 *  # This is a hash comment, which is prohibited.
 *  $hello = 'hello';
 * </code>
 *
 * @category ArPHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.org
 */
class ArWeixin extends ArComponent
{
    // 微信 AppId
    protected $appId;
    // 微信 AppSecret
    protected $appSecret;
    // 微信 token
    protected $token;
    // 微信 请求数据
    protected $rawDataArray;
    // 事件推送
    private static $events = array();

    /**
     * initialization function.
     *
     * @param mixed  $config config.
     * @param string $class  hold class.
     *
     * @return Object
     */
    static public function init($config = array(), $class = __CLASS__)
    {
        $obj = parent::init($config, $class);

        if (empty($obj->config['APPID'])) :
            throw new ArException("wx config mission error : " . "'APPID' required !");
        else :
            $obj->appId = $obj->config['APPID'];
        endif;

        if (empty($obj->config['APPSECRET'])) :
            throw new ArException("wx config mission error : " . "'APPSECRET' required !");
        else :
            $obj->appSecret = $obj->config['APPSECRET'];
        endif;

        if (empty($obj->config['TOKEN'])) :
            throw new ArException("wx config mission error : " . "'TOKEN' required !");
        else :
            $obj->token = $obj->config['TOKEN'];
        endif;

        // 设置curl ssl 请求参数
        arComp('rpc.api')->curlOptions = array(
            CURLOPT_SSL_VERIFYPEER => false,
        );

        arComp('rpc.api')->method = 'post';

        return $obj;

    }

    /**
     * 获取微信服务器推送xml元素值.
     *
     * @param string $name key.
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($this->rawDataArray[$name])) :
            return $this->rawDataArray[$name];
        else :
            return null;
        endif;

    }

    /**
     * upload.
     *
     * @param string $filePath filePath.
     * @param string $type     file type.
     *
     * @return string | false
     */
    public function upload($filePath, $type)
    {
        $postFile = array('media' => '@' . $filePath);

        $accessToken = $this->getAccessToken();

        $result = arComp('rpc.api')->remoteCall(
            'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $accessToken . '&type=' . $type,
            $postFile);

        $resultArray = $this->handlerRemoteData($result);

        return $resultArray;

    }

    /**
     * 群发消息.
     *
     * @param string $filePath filePath.
     * @param string $type     file type.
     *
     * @return string | false
     */
    public function send(array $news)
    {
        $accessToken = $this->getAccessToken();

        $jsonNews = urldecode(json_encode(arComp('format.format')->urlencode($news)));
        $result = arComp('rpc.api')->remoteCall(
            'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token=' . $accessToken,
            $jsonNews);

        $resultArray = $this->handlerRemoteData($result);

        return $resultArray;

    }

    /**
     * 上传素材到服务器.数据格式
     * {
        "thumb_media_id":"qI6_Ze_6PtV7svjolgs-rN6stStuHIjs9_DidOHaj0Q-mwvBelOXCFZiq2OsIU-p",
        "author":"xxx",
        "title":"Happy Day",
        "content_source_url":"www.qq.com",
        "content":"content",
        "digest":"digest",
        "show_cover_pic":"1"
        },
     * @param string $filePath filePath.
     * @param string $type     file type.
     *
     * @return string | false
     */
    public function uploadNews(array $news)
    {
        $articles = array();
        if (arComp('validator.validator')->checkMutiArray($news)) :
            foreach ($news as $new) :
                // 验证数据正确性
                if (count($news) == 7) :
                    $article['thumb_media_id'] = $new[0];
                    $article['author'] = $new[1];
                    $article['title'] = $new[2];
                    $article['content_source_url'] = $new[3];
                    $article['content'] = $new[4];
                    $article['digest'] = $new[5];
                    $article['show_cover_pic'] = $new[6];
                    $articles['articles'][] = $article;
                else :
                    throw new ArException("数组长度不对应");
                endif;
            endforeach;
        else :
            // 验证数据正确性
            $new = $news;
            if (count($new) == 7) :
                $article['thumb_media_id'] = $new[0];
                $article['author'] = $new[1];
                $article['title'] = $new[2];
                $article['content_source_url'] = $new[3];
                $article['content'] = $new[4];
                $article['digest'] = $new[5];
                $article['show_cover_pic'] = $new[6];
                $articles['articles'][] = $article;
            else :
                throw new ArException("数组长度不对应");
            endif;
            $articles['articles'][] = $article;
        endif;

        if (empty($articles)) :
            throw new ArException("提交数据为空");
        endif;

        $accessToken = $this->getAccessToken();

        $jsonArticles = urldecode(json_encode(arComp('format.format')->urlencode($articles)));

        $result = arComp('rpc.api')->remoteCall(
            'https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=' . $accessToken,
            $jsonArticles);

        $resultArray = $this->handlerRemoteData($result);

        return $resultArray;

    }

    /**
     * get Access Token
     *
     * @return string
     */
    public function getAccessToken()
    {
        if (!arComp('cache.file')->get('wx_token')) :
            $result = arComp('rpc.api')->remoteCall('https://api.weixin.qq.com/cgi-bin/token', array('grant_type' => 'client_credential', 'appid' => $this->appId, 'secret' => $this->appSecret));
            $resultArray = $this->handlerRemoteData($result);
            arComp('cache.file')->set('wx_token', $resultArray['access_token'], '7200');
        endif;

        return arComp('cache.file')->get('wx_token');

    }

    /**
     * 获取 关注者 openid 列表
     *
     * @return string
     */
    public function getOpenIdList()
    {
        $accessToken = $this->getAccessToken();

        $result = arComp('rpc.api')->remoteCall('https://api.weixin.qq.com/cgi-bin/user/get?' . 'access_token=' . $accessToken);
        $resultArray = $this->handlerRemoteData($result);

        return $resultArray;

    }

    // 获取jstiket
    public function getJsTicket()
    {
        if (!arComp('cache.file')->get('wx_jsticket')) :
            $accessToken = $this->getAccessToken();
            $result = arComp('rpc.api')->remoteCall('https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&' . 'access_token=' . $accessToken);
            $resultArray = $this->handlerRemoteData($result);
            arComp('cache.file')->set('wx_jsticket', $resultArray['ticket'], $resultArray['expires_in']);
        endif;

        return arComp('cache.file')->get('wx_jsticket');

    }

    /**
     * 创建菜单
     *
     * @return void
     */
    public function createMenu()
    {
        if (empty($this->config['menu'])) :
            throw new ArException("wx config mission error : " . "'menu' required !");
        endif;

        $jsonPostMenu = urldecode(json_encode(arComp('format.format')->urlencode($this->config['menu'])));
        $result = arComp('rpc.api')->remoteCall('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $this->getAccessToken(), $jsonPostMenu);
        $resultArray = $this->handlerRemoteData($result);

        return $resultArray;

    }

    /**
     * check data.
     *
     * @return mixed
     */
    public function handlerRemoteData($data = '')
    {
        if ($data = json_decode($data, true)) :
            if (!empty($data['errcode'])) :
                throw new ArException("wx request error : " . $data['errmsg'] . ', code : ' . $data['errcode']);
            else :
                return $data;
            endif;
        else :
            throw new ArException("wx data parse error , data : " . $data);
        endif;

    }

    /**
     * 检查是否来自微信.
     *
     * @return boolean
     */
    private function checkSignature()
    {
        if (!empty($this->config['notCheckSign'])) :
            return true;
        endif;
        $signature = arGet('signature');
        $timestamp = arGet('timestamp');
        $nonce = arGet('nonce');

        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) :
            arComp('list.log')->record('sign check true');
            return true;
        else :
            arComp('list.log')->record('sign check false');
            return false;
        endif;

    }

    /**
     * 微信回复.
     *
     * @return void
     */
    public function response($type = 'text', $data = array())
    {
        // 分发处理
        $result = call_user_func_array(array($this, 'process' . ucfirst($type)), array($data));
        arComp('list.log')->record($result);
        echo $result;

    }

    /**
     * 数据处理监听.
     *
     * @return void.
     */
    public function listen()
    {
        if ($this->checkSignature()) :
            $this->processWxServerRequest();
            $eventName = strtolower($this->rawDataArray['Event']);
            arComp('list.log')->record(array('ename' => $eventName));
            $this->emit($eventName, '');
        endif;

    }

    /**
     * 处理文本消息.
     *
     * @param string $data msg
     *
     * @return string
     */
    protected function processText($data)
    {
        $tplXmlArray = array(
            'ToUserName' => $this->rawDataArray['FromUserName'],
            'FromUserName' => $this->rawDataArray['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => 'text',
            'Content' => $data,
        );
        arComp('list.log')->record($tplXmlArray);
        return urldecode(arComp('ext.out')->array2xml(arComp('format.format')->urlencode($tplXmlArray), false, 'xml'));

    }

     /**
     * 处理文本消息.
     *
     * @param string $data msg
     *
     * @return string
     */
    protected function processNews($data)
    {
        $tplXmlArray = array(
            'ToUserName' => $this->rawDataArray['FromUserName'],
            'FromUserName' => $this->rawDataArray['ToUserName'],
            'CreateTime' => time(),
            'MsgType' => 'news',
            'Articles' => array(),
        );

        if (arComp('validator.validator')->checkMutiArray($data)) :
            $tplXmlArray['ArticleCount'] = count($data);
            foreach ($data as $news) :
                $tplXmlArray['Articles']['item'][] = array(
                    'Title' => $news[0],
                    'Description' => $news[1],
                    'PicUrl' => $news[2],
                    'Url' => $news[3],
                );
            endforeach;
        else :
            $news = $data;
            $tplXmlArray['ArticleCount'] = "1";
            $tplXmlArray['Articles']['item'][] = array(
                'Title' => $news[0],
                'Description' => $news[1],
                'PicUrl' => $news[2],
                'Url' => $news[3],
            );
        endif;

        arComp('list.log')->record($tplXmlArray);
        $str = urldecode(arComp('ext.out')->array2xml(arComp('format.format')->urlencode($tplXmlArray), false, 'xml'));
        return $str = preg_replace("#<\d+>|</\d+>#", '', $str);

    }

    /**
     * 处理微信拉取数据.
     *
     * @return void
     */
    public function processWxServerRequest()
    {
        // 第一次验证
        $this->weixinFirstCheck();

        $rawData = file_get_contents('php://input');

        arComp('list.log')->record($rawData, 'raw');

        if ($rawData) :
            $xmlArray = arComp('ext.out')->xml2array($rawData, true);
            arComp('list.log')->record(array('xml' => $xmlArray));
            $this->rawDataArray = $xmlArray['xml'];
        else :
            arComp('list.log')->record('raw empty');
            exit('');
        endif;

    }

    /**
     * 第一次验证.
     *
     * @return void
     */
    public function weixinFirstCheck()
    {
        $echostr = arGet('echostr');
        if ($this->checkSignature() && !empty($echostr)) :
            echo $echostr;
            arComp('list.log')->record('check first');
            exit;
        endif;

    }


    /**
     * 第一次关注.
     *
     * @return void
     */

    // public function

    /**
     * 注册各种事件回调函数.
     *
     * @param string   $eventName     事件名称, 如: read, recv.
     * @param function $eventCallback 回调函数.
     *
     * @return void
     */
    public function registerEvent($eventName, $eventCallback)
    {
        if (empty(self::$events[$eventName])) :
            self::$events[$eventName] = array();
        endif;
        array_push(self::$events[$eventName], $eventCallback);
    }

    /**
     * 调用事件回调函数.
     *
     * @param $eventName 事件名称.
     *
     * @return void.
     */
    private static function emit($eventName)
    {
        if (!empty(self::$events[$eventName])) :
            $args = array_slice(func_get_args(), 1);
            arComp('list.log')->record($args);
            arComp('list.log')->record(self::$events[$eventName]);
            if (empty($args)) :
                $args = array();
            endif;
            foreach (self::$events[$eventName] as $callback) :
                call_user_func_array($callback, $args);
            endforeach;
        else :
            arComp('list.log')->record('event empty');
        endif;

    }

}
