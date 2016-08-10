<?php
/**
 * ArPHP A Strong Performence PHP FrameWork ! You Should Have.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  Core.base
 * @author   yc <ycassnr@gmail.com>
 * @license  http://www.arphp.org/licence MIT Licence
 * @version  GIT: 1: coding-standard-tutorial.xml,v 1.0 2014-5-01 18:16:25 cweiske Exp $
 * @link     http://www.arphp.org
 */

/**
 * application
 *
 * default hash comment :
 *
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
class ArApplicationWeb extends ArApplication
{
    // route container
    public $route = array();

    /**
     * start function.
     *
     * @return void
     */
    public function start()
    {
        if (AR_DEBUG && !AR_AS_CMD) :
            arComp('ext.out')->deBug('[APP_WEB_START]');
        endif;
        if (AR_AUTO_START_SESSION && ini_get('session.auto_start') == 0) :
            session_start();
        endif;

        $this->processRequest();

    }

    /**
     * process.
     *
     * @return void
     */
    public function processRequest()
    {
        $this->runController(Ar::getConfig('requestRoute'));

    }

    /**
     * default controller.
     *
     * @param string $route route.
     *
     * @return mixed
     */
    public function runController($route)
    {
        if (AR_DEBUG && !AR_AS_CMD) :
            arComp('ext.out')->deBug('[CONTROLLER_RUN]');
        endif;

        Ar::setConfig('requestRoute', $route);

        if (empty($route['a_c'])) :
            $c = 'Index';
        else :
            $c = ucfirst($route['a_c']);
        endif;

        $this->route['a_c'] = $c;
        $class = $c . 'Controller';

        if (AR_DEBUG && !AR_AS_CMD) :
            arComp('ext.out')->deBug('|CONTROLLER_EXEC:'. $class .'|');
        endif;

        if (class_exists($class)) :
            $this->_c = new $class;
            $this->_c->init();
            $action = ($a = empty($route['a_a']) ? AR_DEFAULT_ACTION : $route['a_a']) . 'Action';
            $this->route['a_a'] = $a;
            if (is_callable(array($this->_c, $action))) :
                try {
                    if (AR_DEBUG && !AR_AS_CMD) :
                        arComp('ext.out')->deBug('|ACTION_RUN:' . $action . '|');
                    endif;
                    $this->_c->$action();
                } catch (ArException $e) {
                    if (!AR_AS_OUTER_FRAME) :
                        throw new ArException($e->getMessage());
                    endif;
                }
            else :
                if (!AR_AS_OUTER_FRAME) :
                    throw new ArException('Action ' . $action . ' not found');
                endif;
            endif;
        endif;

    }

}
