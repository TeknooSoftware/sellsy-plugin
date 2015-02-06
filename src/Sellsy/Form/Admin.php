<?php

namespace UniAlteri\Sellsy\Form;

/**
 * Class Admin
 * Class to configure Wordpress admin to configure this plugin
 * @package UniAlteri\Sellsy\Admin
 */
class Admin 
{
    /**
     * Method to add sellsy javascript for Wordpress admin
     */
    public function addJS()
    {
        wp_enqueue_script(
            'wpsellsyjscsource',
            plugins_url('/js/wp_sellsy.js', SELLSY_WP_PATH_FILE),
            array('jquery'),
            '1.0',
            1
        );

        wp_localize_script(
            'wpsellsyjscsource',
            'ajax_var',
            [
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wpi_ajax_nonce')
            ]
       );
    }

    /**
     * Method to add sellsy css stylesheet for Wordpress admin
     * @param string $hook
     */
    public function addCSS($hook)
    {
        if (is_admin()) {
            if ('toplevel_page_wpi-admPage' != $hook) {
                return;
            }

            wp_register_style(
                'wpsellsystylesadmin',
                plugins_url('/css/wp_sellsy_admin.css', SELLSY_WP_PATH_FILE),
                array(),
                '1.0',
                'screen'
            );

            wp_enqueue_style('wpsellsystylesadmin');
        }
    }

    /**
     * Method to add in the wordpress menu an entry to access to the plugin configuration page
     */
    public function addMenu()
    {
        add_menu_page(
            'WP Sellsy',
            'WP Sellsy',
            'manage_options',
            'wpi-admPage',
            [$this, 'wpi_admPage'],
            plugins_url('/img/sellsy_15.png', SELLSY_WP_PATH_FILE)
        );
    }

    /**
     * Display the admin page to manage Sellsy
     */
    public function page()
    {
        if (is_admin() && current_user_can('manage_options') && is_readable(SELLSY_WP_PATH_INC.'/wp_sellsy-adm-page.php')) {
            include_once SELLSY_WP_PATH_INC.'/wp_sellsy-adm-page.php';
        }
    }
}