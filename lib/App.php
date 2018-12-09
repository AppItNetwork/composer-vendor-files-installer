<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace cvfi\lib;

class App extends BaseObject
{
	private $_controller;
	private $_request;
	private $_helper;
	private $publicAssetFolder = 'cvfi-assets';

	public function init()
	{
		CVFI::setApp($this);

		$this->setHelper();
		$this->setRequest();
		$this->setController();
		$this->setView();
		$this->setAssetToPublic();
	}

    public function getAssetBaseUrl()
    {
        return $this->request->scriptEntryUrl . $this->publicAssetFolder . '/';
    }

    private function setAssetToPublic()
    {
    	$publicAssetFolder = $this->request->scriptEntryPath . DS . $this->publicAssetFolder;
    	if (!is_dir($publicAssetFolder)) {
    		$this->recursiveCopy(INSTALLER_ROOT . 'assets', $publicAssetFolder);
    	}
    }

	private function recursiveCopy($src, $dst) {
		$dir = opendir($src);
		@mkdir($dst);
		while(( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recursiveCopy($src .'/'. $file, $dst .'/'. $file);
				}
				else {
					copy($src .'/'. $file, $dst .'/'. $file);
				}
			}
		}
		closedir($dir);
	}

	public function run()
	{
		$response = $this->_controller->run();
		// pr($this);die;
		echo $response;
	}

	private function setHelper()
	{
		$this->_helper = new Helper;
	}

	public function getHelper()
	{
		return $this->_helper;
	}

	private function setRequest()
	{
		$this->_request = new Request;
	}

	public function getRequest()
	{
		return $this->_request;
	}

	private function setController()
	{
		$controllerName = ucfirst($this->_request->controllerName) . 'Controller';
		$controllerFile = __DIR__ . DS . '..' . DS . 'controllers' . DS . $controllerName . '.php';
		if (file_exists($controllerFile)) {
			$controller = '\\cvfi\\controllers\\'.$controllerName;
			$this->_controller = new $controller([
				'name' => $this->_request->controllerName,
				'action' => $this->_request->actionName,
				'request' => $this->_request,
			]);
		} else {
			$controller = new Controller;
			$controller->setStatusCode(404);
            $controller->sendResponse($controller->statusText, true);
			// throw new Exception("`$controllerFile` does not exists.");
		}
	}

	public function getController()
	{
		return $this->_controller;
	}

	private function setView()
	{
		$view = new View(['controllerName' => $this->_controller->name]);
		$this->_controller->view = $view;
	}
}
