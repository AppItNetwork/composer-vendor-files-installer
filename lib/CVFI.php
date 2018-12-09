<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace cvfi\lib;

class CVFI extends BaseObject
{
	public static $app;

	public static function setApp(App $app)
	{
		self::$app = $app;
	}

    public static function getVersion()
    {
        return '1.0.0';
    }

    // public function handleError($code, $message, $file, $line)
    public static function handleError($code, $message, $file, $line, $context)
    {
        throw new \ErrorException($message, $code, $code, $file, $line);
    }

    public static function handleException($exception)
    {
        if (PHP_SAPI !== 'cli') {
            http_response_code(500);
        }        
        if (self::$app->request instanceof BaseObject && self::$app->request->isAjax) {
            header("Content-Type: application/json; charset=UTF-8", true);
            echo $exception->__toString();
            die;
        }

    	pr($exception->__toString());die;
    }

    public static function handleFatalError()
    {
    	die;
    	// pr(__FUNCTION__);die;
    }
}
