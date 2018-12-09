<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace cvfi\lib;

class Controller extends BaseObject
{
    const FORMAT_HTML = 'html';
    const FORMAT_JSON = 'json';
    const FORMAT_JSONP = 'jsonp';
    
	public $name;
	public $action;
	public $format;
    public $statusText = "OK\n";
    public $_protocolVersion;
    public $defaultAction = 'index';

    public static $httpStatuses = [
        200 => "OK\n",
        400 => "Bad Request\n",
        401 => "Unauthorized\n",
        404 => "Not Found\n",
        500 => "Internal Server Error\n",
    ];

    private $_statusCode = 200;
    private $_view;

	public function init()
	{
		$this->format = self::FORMAT_HTML;
        if (isset($_SERVER['SERVER_PROTOCOL']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.0') {
            $this->_protocolVersion = '1.0';
        } else {
            $this->_protocolVersion = '1.1';
        }
	}

    public function getView()
    {
        return $this->_view;
    }

    public function setView(View $view)
    {
        $this->_view = $view;
    }

    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    public function setStatusCode($value, $text = null)
    {
        if ($value === null) {
            $value = 200;
        }
        $this->_statusCode = (int) $value;
        if ($text === null) {
            $this->statusText = isset(static::$httpStatuses[$this->_statusCode]) ? static::$httpStatuses[$this->_statusCode] : '';
        } else {
            $this->statusText = $text;
        }

        return $this;
    }

	public function run()
	{
		$actionMethod = 'action'.ucfirst($this->action);
        // pr($actionMethod);die;
        $this->setStatusCode(200);
		header("Content-Type: text/html; charset=utf-8");
		if ($this->hasMethod($actionMethod)) {
			if ($this->beforeAction()) {
                $response = $this->$actionMethod();
				if (self::FORMAT_JSON == $this->format) {
					header("Content-Type: application/json; charset=UTF-8", true);
					$response = json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
				} elseif (self::FORMAT_JSONP == $this->format) {
                    header("Content-Type: text/javascript; charset=UTF-8", true);
					if (!isset($response['data'], $response['callback'])) {
			            $this->setStatusCode(400);
						$this->statusText = "Sent request must consists of `data` and `callback` property.\n";
                        $this->sendResponse($this->statusText, true);
					}
					$response = sprintf(
		                '%s(%s);',
		                $response['callback'],
		                json_encode($response['data'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS)
		            );
				}
                return $this->sendResponse($response);
			}
		} else {
            $this->setStatusCode(404);
            $this->sendResponse($this->statusText, true);
        }
	}

    public function beforeAction()
    {
        $request = CVFI::$app->request;
        if (!$request->validateCsrfToken()) {
            if (self::FORMAT_JSON == $this->format) {
                header("Content-Type: application/json; charset=UTF-8", true);
            } elseif (self::FORMAT_JSONP == $this->format) {
                header("Content-Type: text/javascript; charset=UTF-8", true);
            }
            $this->setStatusCode(401);
            if ($request->getIsSameDomain()) {
                $this->setStatusCode(400);
                $this->statusText = "Unable to verify your data submission.\n";
            }
            $this->sendResponse($this->statusText, true);
        }

        return true;
    }

    public function sendResponse($response, $exit = false)
    {
        header("HTTP/{$this->_protocolVersion} {$this->getStatusCode()} {$this->statusText}");
        if ($exit) {
            echo $response;
            die;
        }
        return $response;
    }
}
