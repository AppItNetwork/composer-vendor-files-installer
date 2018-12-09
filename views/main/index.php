<?php
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

	$colors = $model->colors;
	$status = true;
	$output = 'Click on "Start" button to run installer.<br>';
	if ($model->phpIsSafeMode()) {
		$status = false;
		$output .= '<br>'.$colors[2]['o'].'PHP is in safe mode. Unable to proceed installation on this server.'.$colors[2]['c'].'<br>';
	}
	if (!$model->phpProcOpenEnabled()) {
		$status = false;
		$output .= '<br>'.$colors[2]['o'].$this->title.' relies on native PHP function `proc_open`, which is not available on your PHP installation. Unable to proceed installation on this server.'.$colors[2]['c'].'<br>';
	}
?>

<div class="col-md-12">
	<div id="terminal" data-status="<?= $status?>">
		<?= $output?>
	</div>
	<div class="py-2 text-center">
		<button type="button" class="btn btn-primary start" data-output="<?= '$ <i class=\'command\'>'.$model->getCommandList($model->defaultIndex)['command'].'</i><br>'?>" <?= (!$status) ? 'disabled' : ''?>>Start</button>
	</div>
</div>
