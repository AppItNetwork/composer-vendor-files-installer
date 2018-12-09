<?php 
/**
 * @author Hanafi Ahmat (hanafi.ahmat@appitnetwork.com)
 * @copyright Copyright (c) 2013-2018 App It Network
 * @link https://www.AppitNetwork.com
 * @license https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

use cvfi\lib\CVFI;
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->appMetaTags()?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $this->title?></title>
    <link rel="shortcut icon" type="image/x-icon" href="<?= CVFI::$app->getAssetBaseUrl()?>img/favicon.ico">
    <link rel="apple-touch-icon" href="<?= CVFI::$app->getAssetBaseUrl()?>img/favicon.ico">
    <link rel="stylesheet" type="text/css" href="<?= CVFI::$app->getAssetBaseUrl()?>css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?= CVFI::$app->getAssetBaseUrl()?>css/style.css">
</head>
<body>
    <header>
        <div class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container d-flex justify-content-between">
                <a href="#" class="navbar-brand d-flex align-items-center">
                    <strong><?= $this->title?></strong>
                </a>
            </div>
        </div>
    </header>

    <main role="main">
        <div class="py-5">
            <div class="container">
                <div class="row">

                    <?= $content?>
                
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
      <div class="container">
        <span class="text-muted">&copy; <a href="https://www.appitnetwork.com">AppItNetwork.com</a> 2013-<?= date('Y') ?></span>
      </div>
    </footer>

    <script src="<?= CVFI::$app->getAssetBaseUrl()?>js/jquery.min.js"></script>
    <script src="<?= CVFI::$app->getAssetBaseUrl()?>js/bootstrap.bundle.min.js"></script>
    <script src="<?= CVFI::$app->getAssetBaseUrl()?>js/script.js"></script>
</body>
</html>
