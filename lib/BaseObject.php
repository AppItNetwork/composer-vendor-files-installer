<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace cvfi\lib;

class BaseObject
{
	public function __construct($config = [])
	{
		if (!empty($config)) {
			foreach ($config as $attribute => $value) {
				if ($this->has($attribute)) {
					$this->$attribute = $value;
				}
			}
		}
		$this->init();
	}

	public function init() {}

	public function __get($attribute)
	{
		$method = 'get' . ucfirst($attribute);
		if (property_exists($this, $attribute)) {
			return $this->$attribute;
		} elseif (method_exists($this, $method)) {
			return $this->$method();
		}
		throw new Exception("Neither `$attribute` is a property of `".get_called_class()."` nor `".get_called_class()."` has method `$method`");
	}

	public function __set($attribute, $value)
	{
		$method = 'set' . ucfirst($attribute);
		if (property_exists($this, $attribute)) {
			return $this->$attribute = $value;
		} elseif (method_exists($this, $method)) {
			return $this->$method($value);
		}
		throw new Exception("Neither `$attribute` is a property of `".get_called_class()."` nor `".get_called_class()."` has method `$method`");
	}

	public function has($attribute)
	{
		return property_exists($this, $attribute);
	}

	public function hasMethod($method)
	{
		return method_exists($this, $method);
	}
}
