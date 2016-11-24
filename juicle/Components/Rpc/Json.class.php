<?php
/**
 * JuiclePHP
 * PHP version 7
 * @link   http://php.juicler.com
 */
class Json extends Source
{
    /**
     * parse return data.
     *
     * @param string $parseStr not parsed string.
     *
     * @return string
     */
    protected function parse($parseStr)
    {
        return $this->parseJson($parseStr);

    }

    /**
     * parse json.
     *
     * @param string $parseStr parse string.
     *
     * @return Object
     */
    protected function parseJson($parseStr)
    {
        return json_decode($parseStr, 1);

    }

}
