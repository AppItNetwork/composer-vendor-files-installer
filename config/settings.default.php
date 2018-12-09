<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppItNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

return [
	'composerHomePath' => INSTALLER_ROOT . 'runtime' . DS . '.composer_home',
	'composerPharBasePath' => INSTALLER_ROOT . 'runtime',
	'composerPharPath' => INSTALLER_ROOT . 'runtime' . DS . 'composer.phar',
	'composerJsonPath' => INSTALLER_ROOT, // define the folder which contains composer.json for the actual application
	'vendorNoDev' => true,
];