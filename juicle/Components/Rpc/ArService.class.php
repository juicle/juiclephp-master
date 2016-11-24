<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.Component
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.org
 */

/**
 * Service
 *
 * default hash comment :
 *
 * <code>
 *  # This is a hash comment, which is prohibited.
 *  $hello = 'hello';
 * </code>
 *
 * @category ArPHP
 * @package  Core.Component
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.org
 */
class ArService extends ArApi
{
    protected $TAG_MSG_SEP = '___SERVICE_STD_OUT_SEP___';

    protected $remoteWsFile = '';

    /**
     * initialization for component.
     *
     * @param mixed  $config config.
     * @param string $class  instanse class.
     *
     * @return Object
     */
    static public function init($config = array(), $class = __CLASS__)
    {
        $obj = parent::init($config, $class);
        $obj->setRemoteWsFile();
        return $obj;

    }

    /**
     * generate ws file name.
     *
     * @param string $wsFile wsFile.
     *
     * @return mixed
     */
    public function setRemoteWsFile($wsFile = '')
    {
        if (empty($wsFile)) :
            $this->remoteWsFile = empty($this->config['wsFile']) ? arComp('url.route')->host() . '/arws.php' : $this->config['wsFile'];
        else :
            $this->remoteWsFile = $wsFile;
        endif;

    }

    /**
     * use sign auth param.
     *
     * @param array $sign sign auth param.
     *
     * @return mixed
     */
    public function setAuthUserSignature($sign = array())
    {
        $this->remoteQueryUrlSign = $sign;

    }

    /**
     * generate remote ws url.
     *
     * @return mixed
     */
    public function gRemoteWsUrl()
    {
        return $this->remoteWsFile . '?' . http_build_query($this->remoteQueryUrlSign);

    }

    /**
     * magic call.
     *
     * @param string $name ws name.
     * @param array  $args args.
     *
     * @return mixed
     */
    public function __call($name, $args = array())
    {
        $remoteQueryUrlSign = array();
        $remoteQueryData = array();
        if (substr($name, 0, 2) === 'Ws') :
            $remoteQueryData['class'] = ltrim($name, 'Ws');
            $remoteQueryData['method'] = $args[0];
            $remoteQueryData['param'] = empty($args[1]) ? array() : $args[1];
        else :
            throw new ArException("Service do not have a method " . $name);
        endif;

        $this->setAuthUserSignature($remoteQueryUrlSign);

        $postServiceData = array('ws' => $this->encrypt($remoteQueryData));

        return $this->callApi($this->gRemoteWsUrl(), $postServiceData);

    }

    /**
     * exec remote process.
     *
     * @param string $url  url.
     * @param array  $args args.
     *
     * @return mixed
     */
    public function callApi($url, $args = array())
    {
        $response = $this->remoteCall($url, $args, 'post');
        return $this->processResponse($response);

    }

   /**
     * response to client.
     *
     * @param mixed $data response data.
     *
     * @return void
     */
    public function response($data = '')
    {
        echo $this->TAG_MSG_SEP . $this->encrypt($data);
        exit;

    }

    /**
     * process remote server response.
     *
     * @param mixed $response back data.
     *
     * @return mixed
     */
    protected function processResponse($response = '')
    {
        // empty response
        if (empty($response)) :
            throw new ArException('Remote Service Error (  Service Response Empty )', '1012');
        endif;
        // error hanlder
        if (preg_match('#.*error.*on line.*#', $response)) :
            throw new ArException('Remote Service Error ( ' . $response . ' )', '1101');
        endif;

        // std debug info
        if (($pos = strpos($response, $this->TAG_MSG_SEP)) !== false) :
            if ($pos === 0) :
                $response = substr($response, strlen($this->TAG_MSG_SEP));
            else :
                list($stdOutMsg, $response) = explode($this->TAG_MSG_SEP, $response);
                if (AR_DEBUG && !AR_AS_CMD) :
                    arComp('ext.out')->debug('[SERVICE_STD_OUT_MSG]');
                    arComp('ext.out')->debug($stdOutMsg);
                endif;
            endif;
        else :
            throw new ArException('not found response hanlder ', '1011');
        endif;

        // result
        $remoteBackResult = $this->decrypt($response);

        if (is_array($remoteBackResult) && !empty($remoteBackResult['error_msg'])) :
            throw new ArException($remoteBackResult['error_msg'], $remoteBackResult['error_code']);
        endif;

        return $remoteBackResult;

    }

}
