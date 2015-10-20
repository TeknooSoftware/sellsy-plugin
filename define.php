<?php

/**
 * Sellsy Wordpress plugin.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.0
 */

/**
 * Needed constants by this plugin.
 */
!defined('SELLSY_WP_VERSION')
    && define('SELLSY_WP_VERSION', '1.0');

!defined('SELLSY_WP_PATH')
    && define('SELLSY_WP_PATH', __DIR__);

!defined('SELLSY_WP_PATH_FILE')
    && define('SELLSY_WP_PATH_FILE', SELLSY_WP_PATH.'/sellsy-plugin.php');

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
