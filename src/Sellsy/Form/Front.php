<?php

namespace UniAlteri\Sellsy\Wordpress\Form;

use UniAlteri\Sellsy\Wordpress\OptionsBag;
use UniAlteri\Sellsy\Wordpress\Plugin;

/**
 * Class Front
 * Class to configure Wordpress front to use this plugin
 * @package UniAlteri\Sellsy\Form
 */
class Front
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
     * To declare needed javascript in the front
     */
    public function addJS()
    {
        if (!\is_admin()) {
            if ('enableJQuery' == $this->options['WPIloadjQuery']) {
                \wp_deregister_script('jquery');
                \wp_register_script(
                    'jquery',
                    SELLSY_WP_JQUERY_URL,
                    false,
                    SELLSY_WP_JQUERY_VERSION
                );
                \wp_enqueue_script('jquery');
            }

            if ('enableJsValidation' == $this->options['WPIjsValid']) {
                \wp_register_script(
                    'wpsellsyjsvalid',
                    plugins_url('/js/jquery.validate.min.js', SELLSY_WP_PATH_FILE),
                    ['jquery'],
                    '1.0',
                    true
                );

                \wp_enqueue_script('wpsellsyjsvalid');
            }
        }
    }

    /**
     * To declare needed css in front
     */
    public function addCSS()
    {
        if (!\is_admin()) {
            \wp_register_style(
                'wpsellsystyles',
                \plugins_url('/css/wp_sellsy.css', SELLSY_WP_PATH_FILE),
                [],
                '1.0',
                'screen'
            );

            \wp_enqueue_style('wpsellsystyles');
        }
    }

    /**
     * To compute shortcode insert in a page
     * @param string $attr
     * @param null|mixed $content
     */
    public function shortcode($attr, $content = null)
    {
        if (is_readable(SELLSY_WP_PATH_INC.'/front-page.php')) {
            include SELLSY_WP_PATH_INC.'/front-page.php';
        }
    }
}