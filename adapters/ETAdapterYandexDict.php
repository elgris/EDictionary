<?php
// These 'require' only for simple class loading without autoloaders. You should
// use your favourite autoloading mechanism instead.

require_once(dirname(__FILE__) . '/ETAdapterAbstract.php');
/**
 * Adapter for getting translated words from Yandex.Dictionary (Яндекс.Словари).
 * Supports limited translation directions
 *
 * @version 0.2
 * @author elgris
 *
 * @license http://www.opensource.org/licenses/MIT MIT license
 *
 * Copyright (c) 2012 elgris
 */
class ETAdapterYandexDict extends ETAdapterAbstract {

    /**
     * Word that wraps Yandex response
     */
    const WRAPWORD = 'test(';

    /**
     * Array of aliases of translation directions
     * Yandex.Dictionary has its own translation directions which depend on
     * non-russian language
     * For example, 'english-russian' and 'russian-english' directions both use
     * 'en' alias
     *
     * This array is also used for direction validation in translate() method
     *
     * @var array
     */
    protected $_translationAliases = array(
        'en_ru' => 'en', //english-russian
        'ru_en' => 'en', //russian-english
        'fr_ru' => 'fr', //french-russian
        'ru_fr' => 'fr', //russian-french
        'de_ru' => 'de', //deutsch-russian
        'ru_de' => 'de', //russian-deutsch
        'it_ru' => 'it', //italian-russian
        'ru_it' => 'it', //russian-italian
        'es_ru' => 'es', //spanish-russian
        'ru_es' => 'es', //russian-spanish
        'la_ru' => 'la', //latin-russian
        'ru_la' => 'la', //russian-latin
        'uk_ru' => 'uk', //ukrainian-russian
        'ru_uk' => 'uk', //russian-ukrainian
    );

    /**
     * Service base url.
     * For example http://slovari.yandex.ru/
     * This url is used for construction of a request to the service
     *
     * @var string
     */
    protected $_serviceUrl = 'http://suggest-slovari.yandex.ru/';

    /**
     * Default translation direction
     * For example 'en_ru'
     * @var string
     */
    protected $_defaultDirection = 'en';

    /**
     * Get translated text using current Yandex.Dictionary
     *
     * @param string $text - text to be translated
     * @param string $direction - translation direction.
     * If set null, $this->_defaultDirection will be used
     * @return string|false - Returns comma separated list of translation variants.
     * False on error
     */
    public function translate($text, $direction = null) {
        $direction = $direction
            ? $this->_getDirectionAlias($direction)
            : $this->_defaultDirection
        ;
        $curlResource = $this->_getCurlResource(
            $this->_constructRequestUrl($text, $direction)
        );
        $data = curl_exec($curlResource);
        curl_close($curlResource);
        if ($data === false) {
            return false;
        }
        $response = json_decode($this->_unwrapResponse($data));
        $result = $this->_parseResponse($response, $text);
        return $result;
    }

    /**
     * Set default translation direction
     *
     * @param string $defaultDirection
     * @return ETAdapterAbstract
     */
    public function setDefaultDirection($defaultDirection) {
        $this->_defaultDirection = $this->_getDirectionAlias($defaultDirection);
        return $this;
    }

    /**
     * Construct request url for getting translation
     *
     * @return string
     */
    protected function _constructRequestUrl($text, $direction) {
        $text = urlencode($text);
        return "{$this->_serviceUrl}suggest-lingvo?v=2&lang={$direction}&callback=test&part={$text}";
    }

    /**
     * Get an alias for given translation direction
     *
     * @param string $direction
     * @return string
     */
    protected function _getDirectionAlias($direction) {
        if (!isset($this->_translationAliases[$direction])) {
            throw new Exception("Unsupported translation direction $direction");
        }
        return $this->_translationAliases[$direction];
    }

    /**
     * Remove wrapping from Yandex-response
     * Response will be look like "test(blablabla)", so we need to remove
     * "test(" from beginning and ")" from end to get only "blablabla"
     *
     * @param string $response
     * @return string
     */
    protected function _unwrapResponse($response) {
        $startPosition = strlen(self::WRAPWORD);
        $symbolsToExtract = strlen($response) - $startPosition - 1;
        return substr($response, $startPosition, $symbolsToExtract);
    }

    /**
     * Parse response from Yandex.Dictionary
     *
     * @param array $response - response decoded into array
     * @param type $phrase - $phrase to be translated
     * @return string|false - Returns comma separated list of translation variants.
     * False on error
     */
    protected function _parseResponse($response, $phrase) {
        if (!is_array($response) || !isset($response[1])) {
            return false;
        }
        $strings = $response[1];
        foreach ($strings as $string) {
            $translation = explode(' - ', $string);
            if (isset($translation[1]) && trim($translation[0]) == $phrase) {
                return trim($translation[1]);
            }
        }
        return false;
    }

}

