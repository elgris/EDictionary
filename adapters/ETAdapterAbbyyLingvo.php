<?php
// These 'require' only for simple class loading without autoloaders. You should
// use your favourite autoloading mechanism instead.

require_once(dirname(__FILE__) . '/ETAdapterAbstract.php');
require_once(dirname(__FILE__) . '/../lib/simple_html_dom.php');
/**
 * Adapter for getting translated words from ABBYY Lingvo Dictionary
 * Uses HTML-pages parsing from http://lingvopro.abbyyonline.com/
 * so it can slow down translation parsing.
 * Please, use cache or another adapter
 *
 * @version 0.2
 * @author elgris
 * @license http://www.opensource.org/licenses/MIT MIT license
 *
 * Copyright (c) 2012 elgris
 */
class ETAdapterAbbyyLingvo extends ETAdapterAbstract {

    /**
     * Service base url.
     * For example http://slovari.yandex.ru/
     * This url is used for construction of a request to the service
     *
     * @var string
     */
    protected $_serviceUrl = 'http://lingvopro.abbyyonline.com/';

    /**
     * Default translation direction
     * For example 'en_ru'
     * @var string
     */
    protected $_defaultDirection = 'en-ru';

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
            ? $this->_prepareDirection($direction)
            : $this->_defaultDirection
        ;
        $response = file_get_html($this->_constructRequestUrl($text, $direction));
        return $this->_parseResponse($response);
    }

    /**
     * Set default translation direction
     *
     * @param string $defaultDirection
     * @return ETAdapterAbstract
     */
    public function setDefaultDirection($defaultDirection) {
        $this->_defaultDirection = $this->_prepareDirection($defaultDirection);
        return $this;
    }

    /**
     * Construct request url for getting translation
     *
     * @return string
     */
    protected function _constructRequestUrl($text, $direction) {
        $text = urlencode($text);
        return "{$this->_serviceUrl}en/Translate/{$direction}/{$text}";
    }

    /**
     * Prepare translation direction. Replace underscores with dashes
     *
     * @param string $direction
     * @return string
     */
    protected function _prepareDirection($direction) {
        return str_replace('_', '-', $direction);
    }

    /**
     * Parse response from Lingvo Dictionary service
     *
     * @param array $response - Simple HTML DOM Parser object
     * @return string|false - Returns comma separated list of translation variants.
     * False on error
     */
    protected function _parseResponse($response) {
        $translations = $this->_getTranslationsFromDom($response);
        return $translations != null
            ? $this->_parseTranslations($translations)
            : false
        ;
    }

    /**
     * Get DOM-blocks with translations content from whole DOM-tree
     *
     * @param simple_html_dom $dom
     * @return array|false
     */
    protected function _getTranslationsFromDom($dom) {
        $translations = $dom->find('span.translation');
        if (!$translations) {
            $translations = $dom->find('a.show-examples');
        }
        return empty($translations)
            ? false
            : $translations
        ;
    }

    /**
     * Parse array of DOM-blocks with translations into comma-separated
     * translations in a single string
     *
     * @param array $translations
     * @return string
     */
    protected function _parseTranslations(array $translations) {
        $result = array();
        foreach ($translations as $element) {
            $word = trim($element->innertext);
            if ((mb_strlen($word) < 2)
                || (preg_match('/[\|;%$#@!&*()]/i', $word))) {
                continue;
            }
            if (!in_array($word, $result)) {
                $result[] = $word;
            }
        }
        return implode(', ', $result);
    }

}

