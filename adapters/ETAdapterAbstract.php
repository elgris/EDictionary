<?php
/**
 * Abstract adapter for getting translated text from different online-services
 * (for example, Google Translate, Yandex.Dictionary, Bing Translator, etc)
 * This adapter uses cURL library for translating texts via online-services
 *
 * @version 0.2
 * @author elgris
 *
 * @license http://www.opensource.org/licenses/MIT MIT license
 *
 * Copyright (c) 2012 elgris
 */
abstract class ETAdapterAbstract {

    /**
     * Service base url.
     * For example http://slovari.yandex.ru/
     * This url is used for construction of a request to the service
     *
     * @var string
     */
    protected $_serviceUrl = null;

    /**
     * Default translation direction
     * For example 'en_ru'
     * @var string
     */
    protected $_defaultDirection = null;

    /**
     * constructor
     *
     * @param string $serviceUrl - custom url for service adapter
     * @param string $defaultDirection - default direction for translation
     * for example, 'en_ru'
     */
    public function __construct($serviceUrl = null, $defaultDirection = null) {
        if (!function_exists('curl_init')) {
            throw new Exception('cURL library does not found! Please install cURL PHP extension');
        }
        if ($serviceUrl) {
            $this->_serviceUrl = $serviceUrl;
        }
        if ($defaultDirection) {
            $this->setDefaultDirection($defaultDirection);
        }
    }

    /**
     * get translated text using current service
     *
     * @param string $text - text to be translated
     * @param string $direction - translation direction.
     */
    abstract public function translate($text, $direction = null);

    /**
     * set default translation direction
     *
     * @param string $defaultDirection
     * @return ETAdapterAbstract
     */
    public function setDefaultDirection($defaultDirection) {
        $this->_defaultDirection = $defaultDirection;
        return $this;
    }

    /**
     * Returns initialized cURL resource
     *
     * @param $url - URL for sending request
     * @return resource
     */
    protected function _getCurlResource($url) {
        $curlResource = curl_init($url);
        if ($curlResource === false) {
            throw new Exception('Could not initialiaze cURL resource');
        }
        curl_setopt($curlResource, CURLOPT_TIMEOUT, 2);
        curl_setopt($curlResource, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);
        return $curlResource;
    }

}

