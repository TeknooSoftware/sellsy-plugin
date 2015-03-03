<?php

namespace UniAlteri\Sellsy\Wordpress\Form;

use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

/**
 * Class Admin
 * Class to configure Wordpress admin to configure this plugin
 * @package UniAlteri\Sellsy\Admin
 */
class Admin 
{
    /**
     * @var Plugin
     */
    protected $sellsyPlugin;

    /**
     * @var OptionsBag
     */
    protected $options;

    /**
     * @param Plugin $sellsyPlugin
     * @param OptionsBag $options
     */
    public function __construct($sellsyPlugin, $options)
    {
        $this->sellsyPlugin = $sellsyPlugin;
        $this->options = $options;
    }

    /**
     * Method to add sellsy javascript for Wordpress admin
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

            \wp_localize_script(
                'wpsellsyjscsource',
                'ajax_var',
                array(
                    'url' => \admin_url('admin-ajax.php'),
                    'nonce' => \wp_create_nonce('slswp_ajax_nonce')
                )
            );
        }
    }

    /**
     * Method to add sellsy css stylesheet for Wordpress admin
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
     * Method to add in the wordpress menu an entry to access to the plugin configuration page
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
     * Display the admin page to manage Sellsy
     */
    public function page()
    {
        if (\is_admin() && \current_user_can('manage_options') && is_readable(SELLSY_WP_PATH_INC.'/admin-page.php')) {
            include SELLSY_WP_PATH_INC.'/admin-page.php';
        }
    }

    /**
     * Method to generate and display the admin page to configure this plugin
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
                $class = ' ' . $setting['class'];
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

            if (is_readable(SELLSY_WP_PATH_INC . '/admin-setting.php')) {
                include SELLSY_WP_PATH_INC . '/admin-setting.php';
            }
        }
    }
}