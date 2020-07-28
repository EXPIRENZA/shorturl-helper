<?php

namespace Expirenza\short\objects;

/**
 * Class Unique Format
 *
 * @property string $url
 * @property string $code
 */
class UniqueFormat
{
    protected $url;
    protected $code;

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }


}
