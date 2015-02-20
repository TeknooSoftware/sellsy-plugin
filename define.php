<?php
define('SELLSY_WP_VERSION', '1.0');
define('SELLSY_WP_PATH', __DIR__);
define('SELLSY_WP_PATH_FILE', SELLSY_WP_PATH.'/wp_sellsy.php');
define('SELLSY_WP_PATH_INC', __DIR__.'/inc');
define('SELLSY_WP_PATH_LANG', dirname(plugin_basename(__FILE__)).'/lang/');
define('SELLSY_WP_FOLDER', basename(SELLSY_WP_PATH));
define('SELLSY_WP_URL', plugins_url().'/'.SELLSY_WP_FOLDER);
define('SELLSY_WP_URL_INCLUDES', SELLSY_WP_URL.'/inc');
define('SELLSY_WP_API_URL', 'https://apifeed.sellsy.com/0/');
define('SELLSY_WP_SOURCE_URL', 'https://www.sellsy.com/?_f=prospection_prefs&action=sources');
define('SELLSY_WP_WEB_URL', 'https://www.sellsy.com/');
define('SELLSY_WP_WEBAPI_URL', 'https://www.sellsy.com/?_f=prefsApi');
define('SELLSY_WP_JQUERY_URL', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js');
define('SELLSY_WP_JQUERY_VERSION', '1.9.1');