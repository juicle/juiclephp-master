<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
abstract class Cache extends Component{

    /**
     * cache get
     *
     * @param string $key cache key.
     *
     * @return mixed
     */
    abstract function get($key);

    /**
     * cache set.
     *
     * @param string $key   cache key.
     * @param mixed  $value value.
     *
     * @return mixed
     */
    abstract function set($key, $value);

    /**
     * cache del.
     *
     * @param string $key cache key.
     *
     * @return mixed
     */
    abstract function del($key);

    /**
     * cache flush.
     *
     * @return mixed
     */
    abstract function flush();

    /**
     * generate cache key.
     *
     * @param string $keyName keyName.
     *
     * @return string
     */
    protected function generateUniqueKey($keyName)
    {
        return md5($keyName);

    }

    /**
     * encrypt cache data.
     *
     * @param mixed $data cache date.
     *
     * @return string
     */
    protected function encrypt($data)
    {
        return serialize($data);

    }

    /**
     * decrypt cache data.
     *
     * @param mixed $data description.
     *
     * @return mixed
     */
    protected function decrypt($data)
    {
        return unserialize($data);

    }

}
