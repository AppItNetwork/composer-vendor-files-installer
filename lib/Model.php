<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace cvfi\lib;

/*
  execute() function from this class was taken from part of phpshell script.
  It was then customised to suite the needs of this application.

  **************************************************************
  *                     PHP Shell                              *
  **************************************************************

  PHP Shell is an interactive PHP script that will execute any command
  entered.  See the files README, INSTALL, and SECURITY or
  http://phpshell.sourceforge.net/ for further information.

  Copyright (C) 2000-2012 the Phpshell-team

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You can get a copy of the GNU General Public License from this
  address: http://www.gnu.org/copyleft/gpl.html#SEC1
  You can also write to the Free Software Foundation, Inc., 59 Temple
  Place - Suite 330, Boston, MA  02111-1307, USA.
*/
class Model extends BaseObject
{
	public $rows = 25;
	public $columns = 200;
	public $colors = [
		1 => [
			'o' => '<i class="success-output">',
			'c' => '</i>'
		],
		2 => [
			'o' => '<em class="error-output">',
			'c' => '</em>',
		]
	];

	public $composerHomePath;
	public $composerJsonPath;
	public $composerPharBasePath;
	public $composerPharPath;
	public $vendorNoDev;
	
	private $_commandList = [];

	public function init()
	{
		$settingsDefault = INSTALLER_ROOT . 'config' . DS . 'settings.default.php';
		if (file_exists($settingsDefault)) {
			$settingsDefault = include($settingsDefault);
		} else {
			$settingsDefault = [];
		}

		$settings = INSTALLER_ROOT . 'config' . DS . 'settings.php';
		if (file_exists($settings)) {
			$settings = include($settings);
		} else {
			$settings = [];
		}

		$settings = array_merge($settingsDefault, $settings);
		$this->composerHomePath = $settings['composerHomePath'];
		$this->composerJsonPath = $settings['composerJsonPath'];
		$this->composerPharBasePath = $settings['composerPharBasePath'];
		$this->composerPharPath = $settings['composerPharPath'];
		$this->vendorNoDev = $settings['vendorNoDev'];

		$this->populateCommandList();
	}

	public function phpIsSafeMode()
	{
		return ini_get('safe_mode');
	}

	public function phpProcOpenEnabled()
	{
		return function_exists('proc_open');
	}

	private function populateCommandList()
	{
		$this->_commandList = [
			'check-composer' => [
				'dir' => '.',
				'command' => 'ls -lah '.$this->composerPharPath,
				'opts' => ['args' => []],
				'nextIndexIfPassed' => 'update-vendor-files',
				'nextIndexIfFailed' => 'download-composer-setup',
			],
			'download-composer-setup' => [
				'dir' => $this->composerPharBasePath,
				'command' => 'php -r "if (copy(\'https://getcomposer.org/installer\', \'composer-setup.php\')) {echo \'Setup file saved successfully\'; exit(0);} else {echo \'Error saving setup file\'; exit(1);}"',
				'opts' => [
					'args' => [],
				],
				'nextIndexIfPassed' => 'install-composer',
				'nextIndexIfFailed' => false,
			],
			'install-composer' => [
				'dir' => $this->composerPharBasePath,
				'command' => 'php composer-setup.php',
				'opts' => [
					'args' => [],
					'envvars' => 'COMPOSER_HOME='.$this->composerHomePath
				],
				'nextIndexIfPassed' => 'recheck-composer',
				'nextIndexIfFailed' => 'remove-composer-setup',
			],
			'recheck-composer' => [
				'dir' => '.',
				'command' => 'ls -lah '.$this->composerPharPath,
				'opts' => ['args' => []],
				'nextIndexIfPassed' => 'install-vendor-files',
				'nextIndexIfFailed' => 'remove-composer-setup',
			],
			'install-vendor-files' => [
				'dir' => $this->composerJsonPath,
				'command' => 'php '.$this->composerPharPath.' update'.(($this->vendorNoDev) ? ' --no-dev' : ''),
				'opts' => [
					'args' => [], 
					'envvars' => 'COMPOSER_HOME='.$this->composerHomePath
				],
				'nextIndexIfPassed' => 'remove-composer-setup',
				'nextIndexIfFailed' => 'remove-composer-setup',
			],
			'remove-composer-setup' => [
				'dir' => $this->composerPharBasePath,
				'command' => 'rm composer-setup.php',
				'opts' => ['args' => []],
				'nextIndexIfPassed' => false,
				'nextIndexIfFailed' => false,
			],
			'update-vendor-files' => [
				'dir' => $this->composerJsonPath,
				'command' => 'php '.$this->composerPharPath.' update'.(($this->vendorNoDev) ? ' --no-dev' : ''),
				'opts' => [
					'args' => [], 
					'envvars' => 'COMPOSER_HOME='.$this->composerHomePath
				],
				'nextIndexIfPassed' => false,
				'nextIndexIfFailed' => false,
			],
		];
	}

	public function getCommandList($index = null)
	{
		if (!is_null($index)) {
			if (isset($this->_commandList[$index])) {
				return $this->_commandList[$index];
			}
			return ['command' => null];
		}

		return $this->_commandList;
	}

	public $defaultIndex = 'check-composer';

	public function execute($commandListIndex = '')
	{
		$commandListIndex = !empty($commandListIndex) ? $commandListIndex : $this->defaultIndex;
        $colors = $this->colors;
		try {
			$dir = $this->_commandList[$commandListIndex]['dir'];
			$command = $this->_commandList[$commandListIndex]['command'];
			$opts = $this->_commandList[$commandListIndex]['opts'];
			$nextIndexIfPassed = $this->_commandList[$commandListIndex]['nextIndexIfPassed'];
			$nextIndexIfFailed = $this->_commandList[$commandListIndex]['nextIndexIfFailed'];
			$nextIndex = false;
			$command = (!empty($passedCommand)) ? $passedCommand : $command;
			$output = '';
	        if (@chdir($dir)) {
	            if (!$this->phpIsSafeMode()) {
	                putenv('ROWS=' . $this->rows);
	                putenv('COLUMNS=' . $this->columns);
					if (isset($opts['envvars']) && !empty($opts['envvars'])) {
						if (is_array($opts['envvars'])) {
							foreach ($opts['envvars'] as $envvar) {
				                $output .= '$ <i class="command">'.$envvar.'</i><br>';
								putenv($envvar);
							}
						} else {
			                $output .= '$ <i class="command">'.$opts['envvars'].'</i><br>';
							putenv($opts['envvars']);
						}
					}

	                // $output .= '$ <i class="command">'.$command.'</i><br>';
		            $io = [];
		            $p = proc_open(
		                $command,
		                [
		              		1 => ['pipe', 'w'],
		                 	2 => ['pipe', 'w'],
		             	],
		                $io
		            );

		            foreach ($io as $key => $stream) {
			            $outputLine = 0;
			            /* Read output sent to stdout. */
			            while (!feof($stream)) {
			                $line = fgets($stream);
			                if (function_exists('mb_convert_encoding')) {
			                    $line = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
			                }
			                $std = htmlspecialchars($line, ENT_COMPAT, 'UTF-8');
			                if ($outputLine == 0 && $key==1) {
			                	$command = rtrim($std);
			                } elseif ($outputLine == 0 && $key==2 && empty($command)) {
			                	$command = false;
			                }
			                if (!empty($std)) {
			                	$output .= $colors[$key]['o'].$std.$colors[$key]['c'].'<br>';
			                }
			                $outputLine++;
			            }
			            fclose($stream);
			        }

		            proc_close($p);

		            if (!empty($command) && false!==$command) {
			            if ($nextIndexIfPassed && isset($this->_commandList[$nextIndexIfPassed])) {
			            	$next = $this->_commandList[$nextIndexIfPassed];
				            $command = $next['command'] . ' ' . implode(' ', $next['opts']['args']);
			            }
						$nextIndex = $nextIndexIfPassed;
		            } else {
			            if ($nextIndexIfFailed && isset($this->_commandList[$nextIndexIfFailed])) {
			            	$next = $this->_commandList[$nextIndexIfFailed];
				            $command = $next['command'] . ' ' . implode(' ', $next['opts']['args']);
			            }
						$nextIndex = $nextIndexIfFailed;
		            }
		        } else {
		            $output .= $colors[2]['o'].'PHP is in safe mode. Your command was not executed.'.$colors[2]['c'].'<br>';
	            }
	        } else {
	            $output .= $colors[2]['o'].'Could not change to working directory. Your command was not executed.'.$colors[2]['c'].'<br>';
	        }

	        return [$command, ((!empty($output)) ? $output : false), $nextIndex];
		} catch (Exception $e) {
            $output = $colors[2]['o'].'Error: Trying to execute unknown command.'.$colors[2]['c'].'<br>';
	        return [false, $output, false];
		}
	}
}
