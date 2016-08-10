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
 * l
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
class ArUpload extends ArComponent
{
    // upload destination folder
    public $dest = '';
    // upload error
    public $errorMsg = null;
    // upload field
    protected $upField = '';

    /**
     * get errorMsg.
     *
     * @return $mixed
     */
    public function errorMsg()
    {
        return $this->errorMsg;

    }

    // mimemap
    static public $extensionMap = array(
            'img' => array(
                'jpg', 'gif', 'png'
            ),
        );

    /**
     * upload.
     *
     * @param string $upField   upload field.
     * @param string $dest      upload destination.
     * @param string $extension allow file extension.
     *
     * @return mixed
     */
    public function upload($upField, $dest = '', $extension = 'all')
    {
        $this->errorMsg = null;

        $this->upField = $upField;
        if (!empty($_FILES[$this->upField]) && empty($_FILES['error']) && is_uploaded_file($_FILES[$this->upField]['tmp_name'])) :
            if ($extension == 'all' || $this->checkFileType($this->getFileExtensionName($_FILES[$this->upField]['name']), $extension)) :
                $dest = empty($dest) ? arCfg('PATH.UPLOAD') : $dest;
                if (!is_dir($dest)) :
                    mkdir($dest, 0777, true);
                endif;

                $upFileName = $this->generateFileName();

                $destFile = rtrim($dest, DS) . DS . $upFileName;

                if (move_uploaded_file($_FILES[$this->upField]['tmp_name'], $destFile)) :

                else :
                    $this->errorMsg = '上传出错';
                endif;

            endif;

        else :
            $this->errorMsg = "Filed '$upField' invalid";
        endif;

        if (!!$this->errorMsg) :
            return false;
        else :
            return $upFileName;
        endif;

    }

    /**
     * check file type valided.
     *
     * @param string $fileType  fileType.
     * @param string $extension file ext.
     *
     * @return boolean
     */
    protected function checkFileType($extension, $aExtension = 'img')
    {
        if (array_key_exists($aExtension, self::$extensionMap)) :
            if (!in_array($extension, self::$extensionMap[$aExtension])) :
                $this->errorMsg = "仅支持" . implode(',', self::$extensionMap[$aExtension]). "类型";
            endif;
        elseif ($extension != $aExtension) :
            $this->errorMsg ="仅支持{$aExtension}类型";
        endif;

        return !$this->errorMsg;

    }

    /**
     * generate filename.
     *
     * @return string
     */
    protected function generateFileName()
    {
        return md5(time() . rand()) . '.' . $this->getFileExtensionName($_FILES[$this->upField]['name']);

    }

    /**
     * get file extension
     *
     * @return void
     */
    protected function getFileExtensionName($fileName)
    {
        return strtolower(substr($fileName, strrpos($fileName, '.') + 1));

    }

}
