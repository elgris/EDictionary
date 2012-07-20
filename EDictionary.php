<?php
// These 'require' only for simple class loading without autoloaders. You should
// use your favourite autoloading mechanism instead.

require_once(dirname(__FILE__) . '/adapters/ETAdapterAbbyyLingvo.php');
require_once(dirname(__FILE__) . '/adapters/ETAdapterYandexDict.php');

/**
 * Class translates words or phrases in given translation direction.
 * It uses different online services and other stuff derived from
 * ETAdapterAbstract
 *
 * @author elgris
 * @version 0.2
 * @license http://www.opensource.org/licenses/MIT MIT license
 * 
 * Copyright (c) 2012 elgris
 */
class EDictionary
{
    /**
     * Prefix to cinstruct adapter instance by it's name
     */
    const ADAPTER_CLASS_PREFIX = 'ETAdapter';

    /**
     * Array of initialized adapters grouped by adapter alias
     * for example, 'YandexDict' => [instance of ETAdapterYandexDict]
     *
     * @var array
     */
    protected $_adapters = array();

    /**
     * Translate given text. Translates text via online service
     * provided by given adapter.
     *
     * @param string $text - text to be translated
     * @param string $direction - translation direction. For example, "en_ru"
     * @param ETAdapterAbstract|string $adapter - adapter key or instance of
     * adapter class derived from ETAdapterAbstract
     * If string given, it has to represent the postfix of class-adapter
     * For example, if $adapter == 'YandexDict' or 'yandexDict', adapter
     * 'ETAdapterYandexDict' will be used
     *
     * @return string|false - Returns text translation. False on error
     */
    public function translate($text, $direction, $adapter)
    {
        if (is_string($adapter)) {
            $adapter = ucfirst($adapter);
            if (!isset($this->_adapters[$adapter])) {
                $adapterName = self::ADAPTER_CLASS_PREFIX . $adapter;
                $this->_adapters[$adapter] = new $adapterName(null, $direction);
            }
            $adapter = $this->_adapters[$adapter];
        } else if(!($adapter instanceof ETAdapterAbstract)) {
            throw new Exception('Trying to use unknown adapter type');
        }
        return $adapter->translate($text, $direction);
    }
}

