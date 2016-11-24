<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Log extends Lists
{
    // cache path
    protected $logPath;

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

        $obj->logPath = empty($obj->config['logPath']) ? (AR_OUTER_START ? AR_ROOT_PATH . 'Log' . DS : arCfg('PATH.LOG')) : $obj->config['logPath'];

        if(!is_dir($obj->logPath)) :
            mkdir($obj->logPath, 0777, true);
        endif;

        return $obj;

    }

    /**
     * 记录日志:
     *
     * {@source }
     *
     * @param mixed $data  记录信息.
     * @param mixed $level 记录等级.
     *
     * @return mixed
     */
    public function record($data = '', $level = 'info')
    {
        if (is_array($data)) :
            $data = var_export($data, true);
        endif;

        $data = '------' . date('Y-m-d H:i:s', time()) . ' ' . time() . "------\n" . $data . "\n";

        return file_put_contents($this->generateLogFileName($level), $data, FILE_APPEND);

    }

    /**
     * 生成日志文件名称.
     *
     * @return void
     */
    protected function generateLogFileName($level)
    {
        $dirName = $this->logPath . date('Ymd') . DS;
        if(!is_dir($dirName)) :
            mkdir($dirName, 0777, true);
        endif;
        return $dirName . $level . '.log.txt';

    }

}
