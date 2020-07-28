<?php
/**
 * Author: Alexander Moiseykin
 * E-mail: master@cifr.us
 * Date: 01.07.2018
 * Time: 22:01
 */

namespace Expirenza\short;

use Expirenza\short\objects\UniqueFormat;
use Expirenza\short\objects\UniqueResponse;
use InvalidArgumentException;

/**
 * Class Link
 * @package Expirenza\short
 *
 * @property string $apiToken
 * @property string $domainUrl
 * @property bool $debug
 * @property array $errors
 * @property string $lastApiMethod
 *
 */
class Link
{
    protected $apiToken;
    protected $domainUrl;
    protected $debug;
    protected $errors;
    protected $lastApiMethod;

    public function __construct($apiToken, $domainUrl, $debug = false)
    {
        if (empty($apiToken)) {
            throw new InvalidArgumentException('Api token is empty');
        }

        if (empty($domainUrl)) {
            throw new InvalidArgumentException('Domain url is empty');
        }

        $this->apiToken = $apiToken;
        $this->domainUrl = $domainUrl;
        $this->debug = $debug;
    }

    /**
     * Get One random generated url as string.
     *
     * @param string $source
     * @return string
     */
    public function getOne(string $source): string
    {
        return $this->parseExec($this->exec($source, Methods::GET_ONE))[$source] ?? '';
    }


    /**
     * Get Many random generated urls.
     * Need to check result array.
     *
     * @param array $source
     * @return array|bool|UniqueResponse[]
     */
    public function getMany(array $source)
    {
        return $this->parseExec($this->exec($source, Methods::GET_MANY));
    }

    /**
     * Get unique previously generated code objects.
     * Need to check sub-status for every item.
     *
     * @param UniqueFormat[] $source
     * @return mixed
     */
    public function getUniqueMany(array $source)
    {
        $tmpSource = $source;
        $source = [];

        foreach ($tmpSource as $item) {
            if ($item instanceof UniqueFormat) {
                $source[] = [
                    'url' => $item->getUrl(),
                    'code' => $item->getCode(),
                ];
            }
        }

        if (!empty($source)) {
            return $this->parseExec($this->exec($source, Methods::GET_UNIQUE));
        } else {
            return [];
        }
    }

    /**
     * Main helper executor
     *
     * @param array $source
     * @param string $method
     * @return bool|mixed|\stdClass
     */
    protected function exec($source, $method)
    {
        $this->errors = [];
        $this->lastApiMethod = '';

        $apiMethod = '';
        switch ($method) {
            case Methods::GET_ONE:
                $apiMethod = Methods::GET_ONE;
                break;
            case Methods::GET_MANY:
                $apiMethod = Methods::GET_MANY;
                break;
            case Methods::GET_UNIQUE:
                $apiMethod = Methods::GET_UNIQUE;
                break;
            default:
                $apiMethod = Methods::GET_ONE;
        }

        $this->lastApiMethod = $apiMethod;

        $data = [
            'access_token' => $this->apiToken,
            'source' => $source
        ];

        return $this->curl($this->domainUrl.$apiMethod, $data);
    }

    /**
     * cURL driver
     *
     * @param string $urlMethod
     * @param array $data
     * @param int $timeout
     * @return bool|mixed|\stdClass
     */
    private function curl($urlMethod, $data, $timeout = 5)
    {

        /**
         * Initialize CURL
         */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlMethod);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,$timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/json'));


        try {
            /**
             * Production mode
             */
            if ($this->debug == false) {
                $server_output = curl_exec($ch);
                $this->_server_response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $json = json_decode($server_output);

                /**
                 * Parse response from API
                 */
                if (is_object($json)) {
                    return $json;
                } else {
                    $this->errors[] = 'Can not detect reason. Response is not an object';
                }

                return false;
            } else {

                /**
                 * Debug mode imitation
                 */

                $response = new \stdClass();
                $response->status = true;

                switch ($this->lastApiMethod) {
                    case Methods::GET_ONE:
                        $response->message[$data['source']] = 'random_string_0';
                        break;
                    case Methods::GET_MANY:
                        foreach ($data['source'] as $key => $value) {
                            $response->message[$value] = 'random_string_'.$key;
                        }
                        break;
                    case Methods::GET_UNIQUE:

                        foreach ($data['source'] as $key => $item) {

                            $response->message[$key] = new UniqueResponse();
                            $response->message[$key]->setCode($item['code']);
                            $response->message[$key]->setUrl($item['url']);
                            $response->message[$key]->setSuccess(true);

                            $response->message[$key] = $response->message[$key]->asArray();
                        }

                        break;
                    default:
                        $this->errors[] = 'Unrecognized method';
                }

                $response = json_decode(json_encode($response));

                return $response;
            }
        } catch (\Exception $e) {
            $this->errors[] = $e->getMessage().' ('.$e->getFile().' - Line #'.$e->getLine().')';
            return false;
        }
    }

    /**
     * Response parser
     *
     * @param $response
     * @return array|bool|UniqueResponse[]
     */
    protected function parseExec($response)
    {

        $content = false;
        if (isset($response->status) && $response->status == true) {

            switch ($this->lastApiMethod) {
                case Methods::GET_ONE:
                    $content = (array)$response->message;

                    break;
                case Methods::GET_MANY:
                    $content = (array)$response->message;

                    break;
                case Methods::GET_UNIQUE:
                    $content = [];
                    foreach ($response->message as $key => $item) {
                        $content[$key] = new UniqueResponse();
                        $content[$key]->setCode($item->code);
                        $content[$key]->setUrl($item->url);
                        $content[$key]->setSuccess($item->success);
                        //$content[$key] = $content[$key]->asArray();
                    }
                    break;
                default:
                    $this->errors[] = 'Unrecognized method';
            }
        } else {
            $this->errors[] = 'Status is False';
        }

        return $content;
    }

    /**
     * Get Unique Request formatted item
     *
     * @param string $url
     * @param string $code
     * @return UniqueFormat
     */
    public function getUniqueFormatItem($url, $code)
    {
        $object = new UniqueFormat();
        $object->setUrl($url);
        $object->setCode($code);
        return $object;
    }

    /**
     * Get current API key
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Get current domain url
     *
     * @return string
     */
    public function getDomainUrl()
    {
        return $this->domainUrl;
    }

    /**
     * Get errors if exist
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
