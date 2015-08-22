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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.0
 */

namespace UniAlteri\Sellsy\Wordpress\Form;

use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

/**
 * Class Admin
 * Class to prepare the Wordpress administration panel to allow users to configure this plugin.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Admin
{
    /**
     * Object to manipulate to configure the plugin.
     *
     * @var Plugin
     */
    protected $sellsyPlugin;

    /**
     * Object to access and store all dynamics parameters neededby this plugin.
     *
     * @var OptionsBag
     */
    protected $options;

    /**
     * Constructor to initialize this object.
     *
     * @param Plugin     $sellsyPlugin
     * @param OptionsBag $options
     */
    public function __construct($sellsyPlugin, $options)
    {
        $this->sellsyPlugin = $sellsyPlugin;
        $this->options = $options;
    }

    /**
     * Method to load sellsy javascripts in the Wordpress administration panel.
     */
    public function addJS()
    {
        if (\is_admin()) {
            \wp_enqueue_script(
                'jqueryui',
                \plugins_url('/js/jquery-ui.min.js', SELLSY_WP_PATH_FILE),
                array('jquery'),
                '1.0',
                1
            );

            \wp_enqueue_script(
                'uimultiselect',
                \plugins_url('/js/ui.multiselect.js', SELLSY_WP_PATH_FILE),
                array('jquery', 'jqueryui'),
                '1.0',
                1
            );

            \wp_enqueue_script(
                'wpsellsyjscsource',
                \plugins_url('/js/wp_sellsy.js', SELLSY_WP_PATH_FILE),
                array('jquery', 'uimultiselect'),
                '1.0',
                1
            );

            //Needed to perform ajax update and dynamic tests on the sellsy api
            \wp_localize_script(
                'wpsellsyjscsource',
                'ajax_var',
                array(
                    'url' => \admin_url('admin-ajax.php'),
                    'nonce' => \wp_create_nonce('slswp_ajax_nonce'),
                )
            );
        }
    }

    /**
     * Method to add sellsy css stylesheet in the Wordpress administration panel.
     *
     * @param string $hook
     */
    public function addCSS($hook)
    {
        if (\is_admin()) {
            if ('toplevel_page_slswp-admPage' != $hook) {
                return;
            }

            \wp_register_style(
                'wpsellsystylesadmin',
                \plugins_url('/css/wp_sellsy_admin.css', SELLSY_WP_PATH_FILE),
                array(),
                '1.0',
                'screen'
           );

            \wp_enqueue_style('wpsellsystylesadmin');

            \wp_register_style(
                'jqueryuicss',
                \plugins_url('/css/jquery-ui.min.css', SELLSY_WP_PATH_FILE),
                array(),
                '1.0',
                'screen'
            );

            \wp_enqueue_style('jqueryuicss');

            \wp_register_style(
                'multiselect',
                \plugins_url('/css/ui.multiselect.css', SELLSY_WP_PATH_FILE),
                array('jqueryuicss'),
                '1.0',
                'screen'
            );

            \wp_enqueue_style('multiselect');
        }
    }

    /**
     * Method to add in the wordpress administration menu an entry
     * to access to the plugin configuration panel.
     */
    public function addMenu()
    {
        if (\is_admin()) {
            \add_menu_page(
                'WP Sellsy',
                'WP Sellsy',
                'manage_options',
                'slswp-admPage',
                array($this, 'page'),
                \plugins_url('/img/sellsy_15.png', SELLSY_WP_PATH_FILE)
            );
        }
    }

    /**
     * Action called by the Wordpress administration to prepare and render
     * the panel to configure this plugin.
     */
    public function page()
    {
        if (\is_admin() && \current_user_can('manage_options') && is_readable(SELLSY_WP_PATH_INC.'/admin-page.php')) {
            include SELLSY_WP_PATH_INC.'/admin-page.php';
        }
    }

    /**
     * Method to generate HTML form'elements to manage setting in the configuratio panel
     * of this plugin.
     *
     * @param array $setting
     */
    public function displaySettings($setting = array())
    {
        if (\is_admin()) {
            $id = null;
            if (isset($setting['id'])) {
                $id = $setting['id'];
            }

            $class = null;
            if (isset($setting['class'])) {
                $class = ' '.$setting['class'];
            }

            $type = null;
            if (isset($setting['type'])) {
                $type = $setting['type'];
            }

            $std = null;
            if (isset($setting['std'])) {
                $std = $setting['std'];
            }

            $desc = null;
            if (isset($setting['desc'])) {
                $desc = $setting['desc'];
            }

            $choices = array();
            if (isset($setting['choices'])) {
                $choices = $setting['choices'];
            }

            $options = $this->options->toArray();

            if (!isset($options[$id]) && 'checkbox' != $type) {
                $options[$id] = $std;
            } elseif (!isset($options[$id])) {
                $options[$id] = 0;
            }

            if (is_readable(SELLSY_WP_PATH_INC.'/admin-setting.php')) {
                include SELLSY_WP_PATH_INC.'/admin-setting.php';
            }
        }
    }
}
