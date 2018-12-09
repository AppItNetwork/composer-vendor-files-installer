<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace cvfi\lib;

class Cookie extends BaseObject
{
    public $name;
    public $value = '';
    public $domain = '';
    public $expire = 0;
    public $path = '/';
    public $secure = false;
    public $httpOnly = true;

    public $create = false;

    public function init()
    {
        if ($this->create) {
            setcookie($this->name, $this->value, $this->expire, $this->path, $this->domain, $this->secure, $this->httpOnly);
        }
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
