<?php
!defined('SELLSY_WP_VERSION')
    && define('SELLSY_WP_VERSION', '1.0');

!defined('SELLSY_WP_PATH')
    && define('SELLSY_WP_PATH', __DIR__);

!defined('SELLSY_WP_PATH_FILE')
    && define('SELLSY_WP_PATH_FILE', SELLSY_WP_PATH.'/wp_sellsy.php');

!defined('SELLSY_WP_PATH_INC')
    && define('SELLSY_WP_PATH_INC', __DIR__.'/inc');

!defined('SELLSY_WP_PATH_LANG')
    && define('SELLSY_WP_PATH_LANG', dirname(plugin_basename(__FILE__)).'/lang/');

!defined('SELLSY_WP_FOLDER')
    && define('SELLSY_WP_FOLDER', basename(SELLSY_WP_PATH));

!defined('SELLSY_WP_URL')
    && define('SELLSY_WP_URL', plugins_url().'/'.SELLSY_WP_FOLDER);

!defined('SELLSY_WP_API_URL')
    && define('SELLSY_WP_API_URL', 'https://apifeed.sellsy.com/0/');

!defined('SELLSY_WP_WEB_URL')
    && define('SELLSY_WP_WEB_URL', 'https://www.sellsy.com/');


!defined('ABSPATH')
    && define('ABSPATH', __DIR__);

!defined('WPINC')
    && define('WPINC', '/tests/Support');
