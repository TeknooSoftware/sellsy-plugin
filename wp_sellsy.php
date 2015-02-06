<?php
/*
Plugin Name: WP Sellsy
Plugin URI: http://www.sellsy.com/
Description: Le plugin WP prospection Sellsy vous permet d'afficher un formulaire de contact connecté avec votre compte Sellsy. Quand le formulaire sera soumis, un prospect et (optionnellement) une opportunité seront créées sur votre compte Sellsy. Pour activer le plugin, vous devez insérer ci-dessous vos tokens d'API Sellsy, disponibles depuis Réglages puis Accès API. Pour afficher le formulaire sur une page ou dans un post insérez le code [wpsellsy].
Version: 1.1
Author: <a href="mailto:contact@synaptech.fr">synaptech</a>
Author URI: http://www.synaptech.fr
*/

require_once 'define.php';

$composerLoader = require_once 'vendor/autoload.php';

if (class_exists('UniAlteri\Sellsy\Wordpress\Plugin')) {
	//Prepare generator to use curl for the api
	$requestGenerator = new \UniAlteri\Curl\RequestGenerator();

	//Initialize the Sellsy API Client
	$sellsyClient = new \UniAlteri\Sellsy\Client\Client($requestGenerator);

	//Initialize the options bag/manager of this plugin
	$options = new \UniAlteri\Sellsy\OptionsBag();

	//Initialize this plugin
	$wpSellsyPlugin = new \UniAlteri\Sellsy\Wordpress\Plugin($sellsyClient, $options);

	//Initialize views
	$wpSellsyFront = new \UniAlteri\Sellsy\Form\Front($wpSellsyPlugin, $options);
	$wpSellsyAdmin = new \UniAlteri\Sellsy\Form\Admin($wpSellsyPlugin);

	//Configure wordpress to require it to check if CUrl is available in this platforme
	add_action('admin_init', [$wpSellsyPlugin, 'checkCUrlExtensions'], 2);

	//Configure wordpress to customize views to use this plgin
	add_action('wp_enqueue_scripts', [$wpSellsyFront, 'addJS']);
	add_action('wp_enqueue_scripts', [$wpSellsyFront, 'addCSS']);
	add_action('admin_enqueue_scripts', [$wpSellsyAdmin, 'addCSS']);
	add_action('admin_enqueue_scripts', [$wpSellsyAdmin, 'addJS']);
	//todo add_action('admin_enqueue_scripts', array( $this, 'wpi_pointers_styles' ) );

	//Configure wordpress to allow this plugin to add a menu to manage its
	add_action('admin_menu', [$wpSellsyAdmin, 'addMenu']);

	//Configure wordpress to manage disable/uninstall of this plugin
	register_deactivation_hook(SELLSY_WP_PATH_FILE, [$wpSellsyPlugin, 'disablePlugin']);
	add_action('admin_init', function() use ($options, $wpSellsyAdmin) {
			$settings = new \UniAlteri\Sellsy\Form\Settings($options);
			$settings->buildForms([$wpSellsyAdmin, 'displaySettings']);
		},
		5
	);

	//Configure wordpress to allow this module to define a new shortcode
	add_action('init', function() use ($wpSellsyFront) {
		add_shortcode('wpsellsy', [$wpSellsyFront, 'shortcode']);
	});

	//Configure wordpress to load translations needed by this module
	add_action('init', [$wpSellsyPlugin, 'loadTranslation']);

	//Configure wordpress to allow this module to load a widget
	add_action('widgets_init', function() {
		if (class_exists('\UniAlteri\Sellsy\Widget')) {
			register_widget('\UniAlteri\Sellsy\Widget');
		} else {
			wp_die('Error, class \UniAlteri\Sellsy\Widget not found');
		}
	});

	//todo add_action('wp_footer', array(  $this, 'wpi_form_validate' ) );

	//Configure wordpress to manage some ajax requests
	add_action('wp_ajax_wpi_createOppSource', [$wpSellsyPlugin, 'checkOppSource']);
}