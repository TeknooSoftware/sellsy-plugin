<?php

use \Teknoo\Sellsy\Wordpress\OptionsBag;
use \Teknoo\Sellsy\Wordpress\Form\Settings;

if (\is_admin() && \current_user_can('manage_options')):
    if (!empty($_GET['settings-updated'])) {
        if ($this->sellsyPlugin->checkSellsyCredentials()) {
            add_settings_error(OptionsBag::WORDPRESS_SETTINGS_NAME, 'sellSyUpdated', __('Successful connection to the Sellsy API. Sellsy settings updated.', 'wpsellsy'), 'updated');

            if (false !== strpos($this->options[Settings::OPPORTUNITY_CREATION], 'Opportunity')) {
                $sourcesList = $this->sellsyPlugin->getSourcesList();
                if (empty($sourcesList)) {
                    add_settings_error(OptionsBag::WORDPRESS_SETTINGS_NAME, Settings::OPPORTUNITY_SOURCE, __('You must define at least one source, otherwise opportunities will be not generated.', 'wpsellsy'), 'error');
                } else {
                    foreach ($this->sellsyPlugin->checkOppListSources($sourcesList) as $source => $exist) {
                        if (false === $exist) {
                            add_settings_error(
                                OptionsBag::WORDPRESS_SETTINGS_NAME, Settings::OPPORTUNITY_SOURCE,
                                __('The opportunity source does not exist on your account ', 'wpsellsy')
                                    .'<a href="'.SELLSY_WP_WEB_URL.'" target="_blank">Sellsy.com</a>.<br>'
                                    .__('Click here to create it :', 'wpsellsy')
                                    .'<a id="creer_source" href="#" data-label="'.$source.'">'
                                    .__('Create the source ', 'wpsellsy').$source
                                    .'</a><img id="imgloader" src="'.SELLSY_WP_URL
                                    .'/img/loader.gif" alt="" /><br>'
                                    .__('You must create the source to allow plugin to create opportunities.', 'wpsellsy'), 'error');
                        }
                    }
                }
            }
        } else {
            add_settings_error(OptionsBag::WORDPRESS_SETTINGS_NAME, 'sellSyTokens', __('Error: Connection failure to the Sellsy API. Tokens are incorrect.', 'wpsellsy'), 'error');
        }
    }
    ?>
    <div class="wrap">
        <div id="icon-sellsy" class="icon32"><br /></div>
        <h2><?php _e('Wordpress Sellsy '.SELLSY_WP_VERSION, 'wpsellsy'); ?></h2>
        <div class="adminInfo">
            <?php _e('The Wordpress plugin Sellsy allow you to display a contact form connected to your Sellsy account. When the form will be submited, a prospect and optionally an opportunity will be created on your Sellsy account. To enable this plugin, you must define your API token. There are available from', 'wpsellsy').' <a href="'.SELLSY_WP_API_URL.'" target="_blank">'._e('"Settings" then "Api Access".', 'wpsellsy').'</a>'._e('To display the form in a page or a post, you can use the short tag [wpsellsy].', 'wpsellsy')._e(' You can also use ', 'wpsellsy').'<a href="'.admin_url().'widgets.php">Widget</a> '._e('if you want display te form in the sidebar.', 'wpsellsy'); ?>
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
    wp_die(__('You have not rights to display this page.', 'wpsellsy'));
endif;
