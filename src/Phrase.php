<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this
 * source code.
 */

namespace Kdyby\Translation;

/**
 * Object wrapper for message that can store default parameters and related information for translation.
 */
class Phrase
{

    use \Kdyby\StrictObjects\Scream;

    /**
     * @var string
     */
    public $message;

    /**
     * @var int|NULL
     */
    public $count;

    /**
     * @var array
     */
    public $parameters;

    /**
     * @var string|NULL
     */
    public $domain;

    /**
     * @var string|NULL
     */
    public $locale;

    /**
     * @var \Kdyby\Translation\Translator|NULL
     */
    private $translator;


    /**
     * @param string $message
     * @param int|array|NULL $count
     * @param string|array|NULL $parameters
     * @param string|NULL $domain
     * @param string|NULL $locale
     */
    public function __construct($message, $count = null, $parameters = null, $domain = null, $locale = null)
    {
        $this->message = $message;

        if (is_array($count))
        {
            $locale = ($domain !== null) ? (string)$domain : null;
            $domain = ($parameters !== null) ? (string)$parameters : null;
            $parameters = $count;
            $count = null;
        }

        $this->count = $count !== null ? (int)$count : null;
        $this->parameters = (array)$parameters;
        $this->domain = $domain;
        $this->locale = $locale;
    }


    /**
     * @param \Kdyby\Translation\Translator $translator
     * @param int|NULL $count
     * @param array $parameters
     * @param string|NULL $domain
     * @param string|NULL $locale
     *
     * @return string|\Nette\Utils\IHtmlString|\Latte\Runtime\IHtmlString
     */
    public function translate(Translator $translator, $count = null, array $parameters = [], $domain = null, $locale = null)
    {
        if (!is_string($this->message))
        {
            throw new \Kdyby\Translation\InvalidStateException('Message is not a string, type ' . gettype($this->message) . ' given.');
        }

        $count = ($count !== null) ? (int)$count : $this->count;
        $parameters = !empty($parameters) ? $parameters : $this->parameters;
        $domain = ($domain !== null) ? $domain : $this->domain;
        $locale = ($locale !== null) ? $locale : $this->locale;

        return $translator->translate($this->message, $count, (array)$parameters, $domain, $locale);
    }


    /**
     * @internal
     *
     * @param \Kdyby\Translation\Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
    }


    public function __toString()
    {
        if ($this->translator === null)
        {
            return $this->message;
        }

        try
        {
            return (string)$this->translate($this->translator);

        }
        catch (\Exception $e)
        {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }

        return '';
    }


    public function __sleep()
    {
        $this->translator = null;

        return ['message', 'count', 'parameters', 'domain', 'locale'];
    }

}
