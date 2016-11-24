<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Session extends Lists
{
    /**
     * initizlization.
     *
     * @param mixed  $config config.
     * @param string $class  class.
     *
     * @return Object
     */
    static public function init($config = array(), $class = __CLASS__)
    {
        $obj = parent::init($config, $class);

        $obj->setContainer($_SESSION);

        return $obj;

    }

    /**
     * set
     *
     * @param Object &$value object.
     *
     * @return void
     */
    public function setContainer(&$value)
    {
        $this->c = &$value;

    }

}