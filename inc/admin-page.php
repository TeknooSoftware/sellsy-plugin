<?php

use \UniAlteri\Sellsy\Wordpress\OptionsBag;
use \UniAlteri\Sellsy\Wordpress\Form\Settings;

if (\is_admin() && \current_user_can('manage_options')):
    if (!empty($_GET['settings-updated'])) {
        if ($this->sellsyPlugin->checkSellsyCredentials()) {
			add_settings_error(OptionsBag::WORDPRESS_SETTINGS_NAME, 'sellSyUpdated', __('Connexion à l\'API Sellsy réussie. Paramètres Sellsy mis à jour.', 'wpsellsy'), 'updated');

            if (false !== strpos($this->options[Settings::OPPORTUNITY_CREATION], 'Opportunity')){
                $sourcesList = $this->sellsyPlugin->getSourcesList();
                if (empty($sourcesList)) {
                    add_settings_error(OptionsBag::WORDPRESS_SETTINGS_NAME, Settings::OPPORTUNITY_SOURCE, __('Vous devez définir au moins une source sinon les opportunités ne seront pas générées.', 'wpsellsy') , 'error');
                } else {
                    foreach ($this->sellsyPlugin->checkOppListSources($sourcesList) as $source=>$exist) {
                        if (false === $exist) {
                            add_settings_error(OptionsBag::WORDPRESS_SETTINGS_NAME, Settings::OPPORTUNITY_SOURCE, __('La source saisie n\'existe pas pour votre compte sur ', 'wpsellsy') . '<a href="' . SELLSY_WP_WEB_URL . '" target="_blank">Sellsy.com</a>.<br>' . __('Cliquez ici pour créer la source sur votre compte :', 'wpsellsy') . '<a id="creer_source" href="#" data-label="'.$source.'">' . __('Créer la source ', 'wpsellsy') . $source . '</a><img id="imgloader" src="' . SELLSY_WP_URL . '/img/loader.gif" alt="" /><br>' . __('Si vous ne créez pas la source, les opportunités ne seront pas générées.', 'wpsellsy') , 'error');
                        }
                    }
                }

            }
		} else {
			add_settings_error(OptionsBag::WORDPRESS_SETTINGS_NAME, 'sellSyTokens', __('Erreur: Connexion à l\'API Sellsy impossible. Les tokens saisis sont incorrects.', 'wpsellsy'), 'error');
		}
    }
    ?>
    <div class="wrap">
        <div id="icon-sellsy" class="icon32"><br /></div>
        <h2><?php _e('WP Sellsy Prospection '.SELLSY_WP_VERSION, 'wpsellsy'); ?></h2>
        <div class="adminInfo">
            <?php _e('Le plugin WP prospection Sellsy vous permet d\'afficher un formulaire de contact connecté à votre compte Sellsy. Quand le formulaire sera soumis, un prospect et (optionnellement) une opportunité seront créés sur votre compte Sellsy. Pour activer le plugin, vous devez insérer ci-dessous vos tokens d\'API Sellsy, disponibles depuis', 'wpsellsy') . '<a href="' . SELLSY_WP_API_URL . '" target="_blank">' . _e('Réglages puis Accès API.','wpsellsy') . '</a>' . _e('Pour afficher le formulaire sur une page ou dans un post insérez le code [wpsellsy].', 'wpsellsy') . _e(' Vous pouvez aussi utiliser le ', 'wpsellsy') . '<a href="' . admin_url() . 'widgets.php">Widget</a> ' . _e('si vous souhaitez insérer votre formulaire dans la sidebar de votre site.', 'wpsellsy'); ?>
        </div>
        <?php settings_errors(OptionsBag::WORDPRESS_SETTINGS_NAME); ?>
        <form id="wp-sellsy-admform" action="options.php" method="POST">
            <?php
            settings_fields(OptionsBag::WORDPRESS_SETTINGS_NAME);
            if (isset($_GET['page'])) {
                do_settings_sections($_GET['page']);
            }
            submit_button();

            if (function_exists('wp_nonce_field')) {
                wp_nonce_field('slswp_nonce_field', 'slswp_nonce_verify_adm');
            }
            ?>
        </form>
    </div>
<?php else:
    wp_die(__('Vous n\'avez pas les droits suffisants pour accéder à cette page.', 'wpsellsy'));
endif;
