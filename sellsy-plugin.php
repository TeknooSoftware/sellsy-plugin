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
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.0
 */

/*
Plugin Name: Sellsy Wordpress plugin.
Plugin URI: http://teknoo.it/sellsy-plugin
Description: The Wordpress plugin Sellsy allow you to display a contact form connected to your Sellsy account. When the form will be submited, a prospect and optionally an opportunity will be created on your Sellsy account.
Version: 0.8.0
Author: Richard Déloge, Teknoo Software
Author URI: http://agence.net;ua
License: GPLv3 or later, MTT License
 */

/**
 * Wordpress plugin file, called to initialize it.
 */
require_once 'define.php';

$composerLoader = require_once 'vendor/autoload.php';

if (class_exists('Teknoo\Sellsy\Wordpress\Plugin')) {
    //Initialize the Sellsy API Client
    $sellsyClientGenerator = new \Teknoo\Sellsy\Client\ClientGenerator();

    //Initialize the options bag/manager of this plugin
    $options = new \Teknoo\Sellsy\Wordpress\OptionsBag();

    //Initialize this plugin
    $wpSellsyPlugin = new \Teknoo\Sellsy\Wordpress\Plugin($sellsyClientGenerator->getClient(), $options);

    //Initialize views
    $wpSellsyFront = new \Teknoo\Sellsy\Wordpress\Form\Front($wpSellsyPlugin, $options);
    $wpSellsyAdmin = new \Teknoo\Sellsy\Wordpress\Form\Admin($wpSellsyPlugin, $options);

    //Configure wordpress to load options
    add_action(
        'init',
        function () use ($options) {
            $options->registerHooks();
        }
    );

    //Configure wordpress to require it to check if CUrl is available in this platform
    add_action('admin_init', array($wpSellsyPlugin, 'checkCUrlExtensions'), 2);

    //Configure wordpress to customize views to use this plugin
    add_action('wp_enqueue_scripts', array($wpSellsyFront, 'addCSS'));
    add_action('admin_enqueue_scripts', array($wpSellsyAdmin, 'addCSS'));
    add_action('admin_enqueue_scripts', array($wpSellsyAdmin, 'addJS'));

    //Configure wordpress to allow this plugin to add a menu to manage its
    add_action('admin_menu', array($wpSellsyAdmin, 'addMenu'));

    //Configure wordpress to manage disable/uninstall of this plugin
    register_deactivation_hook(SELLSY_WP_PATH_FILE, array($wpSellsyPlugin, 'disablePlugin'));
    add_action('admin_init', function () use ($options, $wpSellsyAdmin, $wpSellsyPlugin) {
            $settings = new \Teknoo\Sellsy\Wordpress\Form\Settings($wpSellsyPlugin, $options);
            $settings->buildForms(function () {}, array($wpSellsyAdmin, 'displaySettings'));
        },
        5
    );

    //Configure wordpress to allow this module to define a new shortcode
    add_action('init', function () use ($wpSellsyFront) {
        add_shortcode('wpsellsy', array($wpSellsyFront, 'shortcode'));
    });

    //Configure wordpress to load translations needed by this module
    add_action('init', array($wpSellsyPlugin, 'loadTranslation'));

    //Configure wordpress to allow this module to load a widget
    add_action('widgets_init', function () {
        if (class_exists('\Teknoo\Sellsy\Wordpress\Widget')) {
            register_widget('\Teknoo\Sellsy\Wordpress\Widget');
        }
    });

    //Configure wordpress to manage some ajax requests
    add_action('wp_ajax_sls_createOppSource', array($wpSellsyPlugin, 'createOppSource'));
}
