<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace cvfi\lib;

use ArrayIterator;

class Collection extends BaseObject implements \IteratorAggregate, \ArrayAccess, \Countable
{
    private $_entities;

    public function __construct($entities = [], $config = [])
    {
        $this->_entities = $entities;
        parent::__construct($config);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->_entities);
    }

    public function count()
    {
        return $this->getCount();
    }

    public function getCount()
    {
        return count($this->_entities);
    }

    public function find($name)
    {
        if (isset($this->_entities[$name])) {
            return $this->_entities[$name];
        }
        $name = str_replace(' ', '-', strtolower(str_replace('_', ' ', $name)));
        return isset($this->_entities[$name]) ? $this->_entities[$name] : null;
    }

    public function get($name)
    {
        return $this->find($name);
    }

    public function getValue($name, $defaultValue = null, $first = true)
    {
        if ($entity = $this->find($name)) {
            if ($entity instanceof BaseObject && $entity->has('value')) {
                return $entity->value;
            }
            if (is_array($entity)) {
                return $first ? reset($entity) : $entity;
            }
        }
        return $defaultValue;
    }

    public function has($name)
    {
        if (!is_null($entity = $this->find($name))) {
            if ($entity instanceof BaseObject && $entity->has('value') && $entity->has('expire')) {
                return $entity->value !== '' && ($entity->expire === null || $entity->expire >= time());
            }
            return true;
        }
        return false;
    }

    public function add($entity, $value = null)
    {
        if ($entity instanceof BaseObject) {
            if ($entity->has('name')) {
                $this->_entities[$entity->name] = $entity;
            }
        } else {
            $name = str_replace(' ', '-', strtolower(str_replace('_', ' ', $entity)));
            if (isset($this->_entities[$name])) {
                $this->_entities[$name][] = $value;
            } else {
                $this->_entities[$name] = [$value];
            }            
        }

        return $this;
    }

    public function set($name, $value = '')
    {
        return $this->add($name, $value);
    }

    public function remove($entity, $removeFromBrowser = true)
    {
        if ($entity instanceof Cookie) {
            $entity->expire = 1;
            $entity->value = '';
            if ($removeFromBrowser) {
                $this->_entities[$entity->name] = $entity;
            } else {
                unset($this->_entities[$entity->name]);
            }
        } else {
            if ($this->has($name)) {
                unset($this->_entities[$name]);
            }
        }
    }

    public function removeAll()
    {
        $this->_entities = [];
    }

    public function toArray()
    {
        return $this->_entities;
    }

    public function fromArray(array $array)
    {
        $this->_entities = $array;
    }

    public function offsetExists($name)
    {
        return $this->has($name);
    }

    public function offsetGet($name)
    {
        return $this->get($name);
    }

    public function offsetSet($name, $value)
    {
        // $this->add($entity);
        $this->add($name, $value);
    }

    public function offsetUnset($name)
    {
        $this->remove($name);
    }
}
