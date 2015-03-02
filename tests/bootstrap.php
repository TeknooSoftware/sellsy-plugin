<?php

defined('RUN_CLI_MODE')
    || define('RUN_CLI_MODE', true);

defined('PHPUNIT')
    || define('PHPUNIT', true);

ini_set('memory_limit', '32M');

date_default_timezone_set('UTC');

error_reporting(E_ALL | E_STRICT);

require_once ('Support/wordpress.php');
require_once ('Support/PHPMailer.php');
require_once ('Support/WP_Widget.php');

require_once (__DIR__.'/../define.php');