<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace cvfi\lib;

class View extends BaseObject
{
	public $controllerName;
	public $folder;
	public $layout;
	public $title = 'Composer Vendor Files Installer';

	public function init()
	{
		$this->layout = __DIR__ . DS . '..' . DS . 'views' . DS . 'layouts' . DS . 'base.php';
		$this->folder = __DIR__ . DS . '..' . DS . 'views' . DS . $this->controllerName;
	}

	public function render($action, $params=[])
	{
		$actionFile = $this->folder . DS . $action . '.php';
		if (file_exists($actionFile)) {
			$content = $this->renderFile($actionFile, $params);
			return $this->renderFile($this->layout, ['content' => $content]);
		}

		throw new Exception($actionFile . ' file does not exists.');
	}

	public function renderFile($file, $params=[])
	{
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        require($file);
        return ob_get_clean();
	}

	public function appMetaTags()
	{
        $request = CVFI::$app->request;
        if ($request instanceof Request) {
            return '<meta name="csrf-param" content="' . $request->csrfParam . '">' . "\n    "
                . '<meta name="csrf-token" content="' . $request->getCsrfToken() . '">' . "\n    "
                . '<meta name="api-endpoint" content="' . $request->getApiEndpoint() . '">' . "\n";
        }

        return '';
	}

	public function csrfInput()
	{
        $request = CVFI::$app->request;
        if ($request instanceof Request) {
            return '<input type="hidden" name="' . $request->csrfParam . '" value="' . $request->getCsrfToken() . '">';
        }

        return '';
	}
}
