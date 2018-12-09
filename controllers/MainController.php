<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace cvfi\controllers;

use cvfi\lib\Controller;
use cvfi\lib\CVFI;
use cvfi\lib\Exception;
use cvfi\lib\Model;

class MainController extends Controller
{
	public function actionIndex()
	{
		$model = new Model;
		return $this->view->render('index', ['model' => $model]);
	}

	public function actionApi()
	{
		$request = CVFI::$app->request;
    	if ($request->isAjax) {		
			$this->format = parent::FORMAT_JSON;
	    	$model = new Model;
	    	// if ($request->isPost) {
		    // 	$data = $request->getBodyParams();
	    	// } elseif ($request->isGet) {
		    // 	$data = $request->getQueryParams();
	    	// } else {
		    // 	throw new Exception('Request is not an accceptable type.');
	    	// }
	    	$data = $request->postParams;
	    	$nextIndex = (isset($data['nextIndex'])) ? $data['nextIndex'] : $model->defaultIndex;
	    	list(
	    		$result['command'],
	    		$result['output'],
	    		$result['nextIndex'],
	    	) = $model->execute($nextIndex);
	    	
	    	return $result;
	    }

	    throw new Exception('Only Ajax request is accceptable.');
	}
}
