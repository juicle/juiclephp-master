<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Lists extends Component
{
    // container
    protected $c = array();

    /**
     * if contains.
     *
     * @param string $key key.
     *
     * @return boolean
     */
    public function contains($key)
    {
        return isset($this->c[$key]);

    }

    /**
     * set.
     *
     * @param string $key   key.
     * @param mixed  $value value.
     *
     * @return void
     */
    public function set($key, $value)
    {
        $this->c[$key] = $value;

    }

    /**
     * get.
     *
     * @param string $key key.
     *
     * @return mixed
     */
    public function get($key)
    {
        $r = null;
        if ($this->contains($key)) :
            $r = $this->c[$key];
        endif;
        return $r;

    }

    /**
     * flush.
     *
     * @return mixed
     */
    public function flush()
    {
        $this->c = array();

    }

}
