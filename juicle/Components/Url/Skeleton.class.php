<?php
declare(strict_types = 1); 
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Skeleton extends Component{
    public $appName = '';
    
    protected $basePath = '';

    public function generateFolders(){
        $folderLists = array(
                $this->basePath,
                PUBLIC_CONFIG_PATH,
                $this->basePath . 'Controller',
                $this->basePath . 'View',
                $this->basePath . 'View' . DS . 'Index',
                $this->basePath . 'Ext',
                $this->basePath . 'Model',
                $this->basePath . 'Conf',
                $this->basePath . 'Public',
            );

        foreach($folderLists as $folder){
            if (!$this->check($folder)){
                if (!@mkdir($folder)){
                    throw new BaseException("folder $folder create failed !");
                }
            }
        }

    }

    
    public function generateFiles(){
        $fileLists = array(
            $this->basePath . 'Controller' . DS . 'IndexController.class.php' => '<?php

/**
 * Default Controller of webapp.
 */
class IndexController extends Controller
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
    <h1>Hello, JuiclePHP ! </h1>
    this is your view file !
</html>
',
        $this->basePath . 'Conf' . DS . 'app.config.php' => '<?php

return array(
);',

        PUBLIC_CONFIG_PATH . 'public.config.php' => '<?php

return array(
    \'moduleLists\' => array(
        \'' . $this->appName . '\'
    ),
);',

            );

        foreach($fileLists as $file => $content){
            if (!$this->check($file)) :
                file_put_contents($file, $content);
            endif;
        }


    }

    
    public function check($file){
        return is_file($file) || is_dir($file);

    }

   
    public function generate($appName = ''){
        if (empty($appName) && $appGlobalConfig = Ju::import(PUBLIC_CONFIG_PATH . 'public.config.php', true)){
            if (empty($appGlobalConfig['moduleLists'])){
                throw new BaseException("can not find param 'moduleLists'!");
            }
            $moduleLists = $appGlobalConfig['moduleLists'];
            foreach ($moduleLists as $moduleName){
                $this->generate($moduleName);
            }
        }

        $this->appName = $appName ? $appName : DEFAULT_APP_NAME;

        $this->basePath = ROOT_PATH . $this->appName . DS;

        if (!$this->check($this->basePath)){
            $this->generateFolders();
            $this->generateFiles();
        }

    }

    
    public function generateCmdFile(){
        $folderMan = CMD_PATH;
        $folderConf = $folderMan . 'Conf' . DS;
        $folderBin = $folderMan . 'Bin' . DS;
        $configFile = $folderConf . 'app.config.ini';
        if (!$this->check($folderMan)){
            mkdir($folderMan);
        }
        if (!$this->check($folderConf)){
            mkdir($folderConf);
        }
        if (!$this->check($folderBin)){
            mkdir($folderBin);
        }
        if (!$this->check($configFile)){
            file_put_contents($configFile, ';cmd config file
listen_port=10008
listen_ip=127.0.0.1');
        }

    }

    
    public function generateIntoOther()
    {
        $folderMan = MAN_PATH;
        $folderConf = $folderMan . 'Conf' . DS;
        $configFile = $folderConf . 'public.config.php';
        if (!$this->check($folderMan)){
            mkdir($folderMan);
        }
        if (!$this->check($folderConf)){
            mkdir($folderConf);
        }

        if (!$this->check($configFile)){
            file_put_contents($configFile, '<?php

return array(
    );');

        }

    }

}
