<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

if ('dev'==APP_ENV) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('INSTALLER_ROOT') or define('INSTALLER_ROOT', __DIR__ . DS . '..' . DS);

// global debug nice print_r function
// from CakePHP
function pr($var=null) {
    $template = PHP_SAPI !== 'cli' ? '<pre class="pr">%s</pre>' : "\n%s\n\n";
    printf($template, trim(print_r($var, true)));
}

require 'autoloader.php';

set_exception_handler(['\cvfi\lib\CVFI', 'handleException']);
set_error_handler(['\cvfi\lib\CVFI', 'handleError']);
register_shutdown_function(['\cvfi\lib\CVFI', 'handleFatalError']);
