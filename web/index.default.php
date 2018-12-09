<?php
define('APP_ENV', 'dev');
// define('APP_ENV', 'prod');

require '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'bootstrap.php';

(new \cvfi\lib\App)->run();