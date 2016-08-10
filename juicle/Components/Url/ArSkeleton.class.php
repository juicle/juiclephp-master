<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.Component.Url
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.org
 */

/**
 * default app generate
 *
 * default hash comment :
 *
 * <code>
 *  # This is a hash comment, which is prohibited.
 *  $hello = 'hello';
 * </code>
 *
 * @category ArPHP
 * @package  Core.Component.Url
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  Release: @package_version@
 * @link     http://www.arphp.org
 */
class ArSkeleton extends ArComponent
{
    // appName
    public $appName = '';
    // basepath of app
    protected $basePath = '';

    /**
     * generator.
     *
     * @return mixed
     */
    public function generateFolders()
    {
        $folderLists = array(
                $this->basePath,
                AR_PUBLIC_CONFIG_PATH,
                $this->basePath . 'Controller',
                $this->basePath . 'View',
                $this->basePath . 'View' . DS . 'Index',
                $this->basePath . 'Ext',
                $this->basePath . 'Model',
                $this->basePath . 'Conf',
                $this->basePath . 'Public',
            );

        foreach($folderLists as $folder) :
            if (!$this->check($folder)) :
                if (!@mkdir($folder)) :
                    throw new ArException("folder $folder create failed !");
                endif;
            endif;
        endforeach;

    }

    /**
     * files.
     *
     * @return mixed
     */
    public function generateFiles()
    {
        $fileLists = array(
            $this->basePath . 'Controller' . DS . 'IndexController.class.php' => '<?php
/**
 * Powerd by ArPHP.
 *
 * Controller.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Controller of webapp.
 */
class IndexController extends ArController
{
    /**
     * just the example of get contents.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->display();

    }

}',
        $this->basePath . 'Model' . DS . 'MyModel.class.php' => '<?php
/**
 * Powerd by ArPHP.
 *
 * Model.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */

/**
 * Default Model of webapp.
 */
class MyModel extends ArModel
{

    static public function model($class = __CLASS__)
    {
        return parent::model($class);

    }

    public function yourFunction()
    {
        echo "this is your funciton";

    }

}',
        $this->basePath . 'View' . DS . 'Index' . DS . 'index.php' => '<html>
    <h1>Hello, ArPHP ! </h1>
    this is your view file !
</html>
',
        $this->basePath . 'Conf' . DS . 'app.config.php' => '<?php
/**
 * Ar default app config file.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
return array(
);',

        AR_PUBLIC_CONFIG_PATH . 'public.config.php' => '<?php
/**
 * Ar default public config file.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
return array(
    \'moduleLists\' => array(
        \'' . $this->appName . '\'
    ),
);',

            );

        foreach($fileLists as $file => $content) :
            if (!$this->check($file)) :
                file_put_contents($file, $content);
            endif;
        endforeach;


    }

    /**
     * check file exists.
     *
     * @param string $file file.
     *
     * @return boolean
     */
    public function check($file)
    {
        return is_file($file) || is_dir($file);

    }

    /**
     * generate.
     *
     * @param string $appName appName.
     *
     * @return mixed
     */
    public function generate($appName = '')
    {
        if (empty($appName) && $appGlobalConfig = Ar::import(AR_PUBLIC_CONFIG_PATH . 'public.config.php', true)) :
            if (empty($appGlobalConfig['moduleLists'])) :
                throw new ArException("can not find param 'moduleLists'!");
            endif;
            $moduleLists = $appGlobalConfig['moduleLists'];
            foreach ($moduleLists as $moduleName) :
                $this->generate($moduleName);
            endforeach;
        endif;

        $this->appName = $appName ? $appName : AR_DEFAULT_APP_NAME;

        $this->basePath = AR_ROOT_PATH . $this->appName . DS;

        if (!$this->check($this->basePath)) :
            $this->generateFolders();
            $this->generateFiles();
        endif;

    }

    /**
     * generate cmd file.
     *
     * @return void
     */
    public function generateCmdFile()
    {
        $folderMan = AR_CMD_PATH;
        $folderConf = $folderMan . 'Conf' . DS;
        $folderBin = $folderMan . 'Bin' . DS;
        $configFile = $folderConf . 'app.config.ini';
        if (!$this->check($folderMan)) :
            mkdir($folderMan);
        endif;
        if (!$this->check($folderConf)) :
            mkdir($folderConf);
        endif;
        if (!$this->check($folderBin)) :
            mkdir($folderBin);
        endif;
        if (!$this->check($configFile)) :
            file_put_contents($configFile, ';cmd config file
listen_port=10008
listen_ip=127.0.0.1');
        endif;

    }

    /**
     * ar outer start
     *
     * @return void
     */
    public function generateIntoOther()
    {
        $folderMan = AR_MAN_PATH;
        $folderConf = $folderMan . 'Conf' . DS;
        $configFile = $folderConf . 'public.config.php';
        if (!$this->check($folderMan)) :
            mkdir($folderMan);
        endif;
        if (!$this->check($folderConf)) :
            mkdir($folderConf);
        endif;

        if (!$this->check($configFile)) :
            file_put_contents($configFile, '<?php
/**
 * Ar default public config file.
 *
 * @author ycassnr <ycassnr@gmail.com>
 */
return array(
    );');

        endif;

    }

}
