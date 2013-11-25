<?php

namespace HtmlForm\Abstracts;

/**
 * Class Addable
 * @package HtmlForm\Abstracts
 *
 * @method Addable addTextbox($name, $label, $args = array())
 * @method Addable addEmail($name, $label, $args = array())
 * @method Addable addNumber($name, $label, $args = array())
 * @method Addable addRange($name, $label, $min, $max, $args = array())
 * @method Addable addUrl($name, $label, $args = array())
 * @method Addable addHidden($name, $label, $args = array())
 * @method Addable addPassword($name, $label, $args = array())
 * @method Addable addSelect($name, $label, $options, $args = array())
 * @method Addable addRadio($name, $label, $options, $args = array())
 * @method Addable addCheckbox($name, $label, $options, $args = array())
 * @method Addable addText($name, $html)
 * @method Addable addHoneypot($args = array())
 * @method Addable addButton($name, $buttonText, $args = array());
 * @method Addable addSubmit($name, $buttonText, $args = array());
 */
abstract class Addable
{
	/**
	 * @var array Form elements that have been added
	 * to the form in sequencial order
	 */
	protected $elements = array();

    /**
     * @var array A cache of ReflectionClasses
     */
    private static $rcCache = array();

    /**
     * Takes care of methods like addTextbox(),
     * addSelect(), etc...
     *
     * @param  string $method Called method
     * @param  array $args Arguments passed to the method
     * @throws \Exception
     * @return self
     */
	public function __call($method, $args)
	{
		if ($className = $this->findClass($method)) {
			$reflect  = self::reflectionClass($className);
			$element = $reflect->newInstanceArgs($args);
			$this->elements[] = $element;
		} else {
			throw new \BadMethodCallException("`{$method}()` does not exist on this object.");
		}
		return $this;
	}

    /**
     * Retrieve a reflection class instance for the given class name
     * @param string $className The class name to get an RC instance for
     * @return \ReflectionClass
     */
    private static function reflectionClass($className)
    {
        if (!isset(self::$rcCache[$className])) {
            self::$rcCache[$className] = new \ReflectionClass($className);
        }
        return self::$rcCache[$className];
    }

	/**
	 * Based on a passed method name, figure out
	 * if there is a cooresponding HtmlForm element.
	 * 
	 * @param  string $method Called method
	 * @return string Class name (if there is one)
	 */
	protected function findClass($method)
	{
		if (!preg_match("/^add([a-zA-Z]+)/", $method, $matches)) {
			return false;
		}

		$className = "\\HtmlForm\\Elements\\{$matches[1]}";

		if (!class_exists($className)) {
			return false;
		}

		return $className;
	}
}