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
	$wpSellsy = new \UniAlteri\Sellsy\Wordpress\Plugin();
}