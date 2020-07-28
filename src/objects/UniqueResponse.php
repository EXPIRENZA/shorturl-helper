<?php


namespace Expirenza\short\objects;

/**
 * Class UniqueResponse
 *
 * @package Expirenza\short\objects
 *
 * @property string $url
 * @property string $code
 * @property bool $success
 */
class UniqueResponse
{

    protected $url;
    protected $code;
    protected $success;

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

    /**
     * @return mixed
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param mixed $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * Get array format of this object
     *
     * @return array
     */
    public function asArray()
    {
        return [
            'url' => $this->getUrl(),
            'code' => $this->getCode(),
            'success' => $this->getSuccess(),
        ];
    }


}
