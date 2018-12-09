<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace cvfi\lib;

class Request extends BaseObject
{
    const HEADER_CSRF_NAME = 'X_CSRF_TOKEN';

    public $controllerName;
    public $actionName;
    public $queryParams = [];
    public $postParams = [];
    public $csrfParam = 'cvfi_csrf';
    public $csrfCookieParam = ['httpOnly' => true];

    protected $scriptEntryPath;
    protected $scriptEntryUrl;
    protected $scriptUrl;

    private $_csrfToken;
    private $_method;
    private $_serverName;
    private $_contentType;
    private $_cookieCollection;
    private $_headerCollection;

    public function init()
    {
        $this->_method = $_SERVER['REQUEST_METHOD'];
        $this->_serverName = $_SERVER['SERVER_NAME'];
        $this->_contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : 'text/html';
        
        $this->scriptUrl = $_SERVER['SCRIPT_NAME'];
        $this->scriptEntryPath = substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], DS));
        $this->scriptEntryUrl = substr($_SERVER['SCRIPT_NAME'], 0, (strrpos($_SERVER['SCRIPT_NAME'], '/') + 1));
        
        $this->processRequests();
        $this->getCookies();
        $this->getHeaders();
    }

    private function processRequests()
    {
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->postParams[$key] = stripslashes($value);
            }
        }
        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                $this->queryParams[$key] = stripslashes($value);
            }
        }
        $this->controllerName = 'main';
        $this->actionName = 'index';
        if (!empty($this->queryParams) && isset($this->queryParams['r'])) {
            $route = explode('/', ltrim($this->queryParams['r'], '/'));
            if (!empty($route[0])) {
                $this->controllerName = strtolower($route[0]);
                if (isset($route[1])) {
                    if (!empty($route[1])) {
                        $queryStartPosition = strpos($route[1], '?');
                        $this->actionName = strtolower($route[1]);
                        if ($queryStartPosition>0) {
                            $this->actionName = substr($this->actionName, 0, $queryStartPosition);
                        }
                    }
                }
            }
        }
    }

    public function getBodyParam($param = null) 
    {
        if (!is_null($param)) {
            return isset($this->postParams[$param]) ? $this->postParams[$param] : '';
        }
        $this->postParams;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function getServerName()
    {
        return $this->_serverName;
    }

    public function getContentType()
    {
        return $this->_contentType;
    }

    public function getIsAjax()
    {
        return $this->getHeaders('X_REQUESTED_WITH') === 'XMLHttpRequest';
    }

    public function getReferer()
    {
        return $this->getHeaders('REFERER');
    }

    public function getIsSameDomain()
    {
        return strrpos($this->getHeaders('REFERER'), $this->getHeaders('host').'/');
    }

    public function getApiEndpoint()
    {
        return $this->scriptUrl . '?r=main/api';
    }

    private function getCookies($nameToGet = null)
    {
        if (is_null($this->_cookieCollection)) {
            $cookies = [];
            if (!empty($_COOKIE)) {
                foreach ($_COOKIE as $key => $value) {
                    $cookies[$key] = new Cookie([
                        'domain' => $this->_serverName,
                        'name' => $key,
                        'value' => $value,
                    ]);
                }
            } else {
                $options = $this->csrfCookieParam;
                $cookies[$this->csrfParam] = new Cookie(array_merge($options, [
                    'domain' => $this->_serverName,
                    'name' => $this->csrfParam,
                    'expire' => time() + 3600,
                    'value' => $this->getCsrfToken(),
                    'create' => true,
                ]));
            }
            $this->_cookieCollection = new Collection($cookies);
        }
        if (!is_null($nameToGet)) {
            return $this->_cookieCollection[$nameToGet];
        }
        return $this->_cookieCollection;
    }

    public function getHeaders($nameToGet = null)
    {
        if (is_null($this->_headerCollection)) {
            $this->_headerCollection = new Collection();
            if (function_exists('getallheaders')) {
                $headers = getallheaders();
                foreach ($headers as $name => $value) {
                    $this->_headerCollection->add($name, $value);
                }
            } elseif (function_exists('http_get_request_headers')) {
                $headers = http_get_request_headers();
                foreach ($headers as $name => $value) {
                    $this->_headerCollection->add($name, $value);
                }
            } else {
                foreach ($_SERVER as $name => $value) {
                    if (strncmp($name, 'HTTP_', 5) === 0) {
                        $name = substr($name, 5);
                        $this->_headerCollection->add($name, $value);
                    }
                }
            }
        }
        if (!is_null($nameToGet)) {
            return $this->_headerCollection->getValue($nameToGet);
        }
        return $this->_headerCollection;
    }

    public function getCsrfToken()
    {
        if ($this->_csrfToken === null) {
            $token = $this->loadCsrfToken();
            if (empty($token)) {
                $token = $this->generateCsrfToken();
            }
            $this->_csrfToken = CVFI::$app->helper->maskToken($token);
        }

        return $this->_csrfToken;
    }

    protected function loadCsrfToken()
    {
        return (!is_null($this->_cookieCollection)) ? $this->_cookieCollection->getValue($this->csrfParam) : '';
    }

    protected function generateCsrfToken()
    {
        $token = CVFI::$app->helper->generateRandomString();
        return $token;
    }

    public function validateCsrfToken($clientSuppliedToken = null)
    {
        $method = $this->getMethod();
        // only validate CSRF token on non-"safe" methods https://tools.ietf.org/html/rfc2616#section-9.1.1
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        $trueToken = $this->getCsrfToken();

        if ($clientSuppliedToken !== null) {
            return $this->validateCsrfTokenInternal($clientSuppliedToken, $trueToken);
        }

        return $this->validateCsrfTokenInternal($this->getBodyParam($this->csrfParam), $trueToken) ||
            $this->validateCsrfTokenInternal($this->getHeaderCsrfToken(), $trueToken);
    }

    public function getHeaderCsrfToken()
    {
        return $this->getHeaders(static::HEADER_CSRF_NAME);
    }

    private function validateCsrfTokenInternal($clientSuppliedToken, $trueToken)
    {
        if (!is_string($clientSuppliedToken)) {
            return false;
        }

        return CVFI::$app->helper->unmaskToken($clientSuppliedToken) === CVFI::$app->helper->unmaskToken($trueToken);
    }
}
