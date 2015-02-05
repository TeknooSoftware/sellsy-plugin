<?php
/*
Plugin Name: WP Sellsy
Plugin URI: http://www.sellsy.com/
Description: Le plugin WP prospection Sellsy vous permet d'afficher un formulaire de contact connecté avec votre compte Sellsy. Quand le formulaire sera soumis, un prospect et (optionnellement) une opportunité seront créées sur votre compte Sellsy. Pour activer le plugin, vous devez insérer ci-dessous vos tokens d'API Sellsy, disponibles depuis Réglages puis Accès API. Pour afficher le formulaire sur une page ou dans un post insérez le code [wpsellsy].
Version: 1.1
Author: <a href="mailto:contact@synaptech.fr">synaptech</a>
Author URI: http://www.synaptech.fr
*/

define( 'WPI_VERSION', '1.2' );
define( 'WPI_PATH', dirname( __FILE__ ) );
define( 'WPI_PATH_INC', dirname( __FILE__) . '/inc' );
define( 'WPI_PATH_LANG', dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
define( 'WPI_FOLDER', basename( WPI_PATH ) );
define( 'WPI_URL', plugins_url() . '/' . WPI_FOLDER );
define( 'WPI_URL_INCLUDES', WPI_URL . '/inc' );
define( 'WPI_API_URL', 'https://apifeed.sellsy.com/0/' );
define( 'WPI_SOURCE_URL', 'https://www.sellsy.com/?_f=prospection_prefs&action=sources' );
define( 'WPI_WEB_URL', 'https://www.sellsy.com/' );
define( 'WPI_WEBAPI_URL', 'https://www.sellsy.com/?_f=prefsApi' );
define( 'WPI_JQUERY_URL', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js' );
define( 'WPI_JQUERY_VERSION', '1.9.1' );

if ( !class_exists( 'wp_sellsyClass' ) ) {

	class wp_sellsyClass {

		function __construct() {

			add_action( 'wp_enqueue_scripts', array( $this, 'wpi_addJS' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wpi_addCSS' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'wpi_add_adminCSS' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'wpi_add_adminJS' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'wpi_pointers_styles' ) );
			add_action( 'admin_menu', array( $this, 'wpi_adm_pages_callback' ) );
			register_deactivation_hook( __FILE__, 'wpi_on_deactivate_callback' );
			//add_action( 'admin_init', array( $this, 'wpi_restrict_admin' ), 1 );
			add_action( 'admin_init', array( $this, 'wpi_check_cURL' ), 2 );
			add_action( 'admin_init', array( $this, 'wpi_register_settings' ), 5 );
			add_action( 'init', array( $this, 'wpi_shortcode' ) );
			add_action( 'init', array( $this, 'wpi_loadLang' ) );
			add_action( 'widgets_init', array( $this, 'wpi_widget' ) );
			add_action( 'wp_footer', array(  $this, 'wpi_form_validate' ) );
			add_action( 'wp_ajax_wpi_createOppSource', array(  $this, 'wpi_createOppSource' ) );

		}

		function wpi_addJS() {

			if ( !is_admin() ) {
				$options = get_option( 'wpsellsy_options' );
				if ( isset( $options['WPIloadjQuery'] ) AND $options['WPIloadjQuery'] == 'choice1' ) {
					wp_deregister_script( 'jquery' );
					wp_register_script( 'jquery', WPI_JQUERY_URL, false, WPI_JQUERY_VERSION );
					wp_enqueue_script( 'jquery');
				}
				if ( isset( $options['WPIjsValid'] ) AND $options['WPIjsValid'] == 'choice1' ) {
					wp_register_script( 'wpsellsyjsvalid', plugins_url( '/js/jquery.validate.min.js', __FILE__ ), array( 'jquery' ), '1.0', true );
					wp_enqueue_script( 'wpsellsyjsvalid' );
				}

			}

		}

		function wpi_add_adminJS(){

			wp_enqueue_script( 'wpsellsyjscsource', plugins_url( '/js/wp_sellsy.js', __FILE__ ), array( 'jquery' ), '1.0', 1  );
			wp_localize_script( 'wpsellsyjscsource', 'ajax_var', array(
					'url' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'wpi_ajax_nonce' )
				)
			);

		}

		function wpi_addCSS() {

			if ( !is_admin() ) {
				wp_register_style( 'wpsellsystyles', plugins_url( '/css/wp_sellsy.css', __FILE__ ), array(), '1.0', 'screen' );
				wp_enqueue_style( 'wpsellsystyles' );
			}

		}

		function wpi_add_adminCSS( $hook ) {

			if ( is_admin() ) {
				if ( 'toplevel_page_wpi-admPage' != $hook )
					return;
				wp_register_style( 'wpsellsystylesadmin', plugins_url( '/css/wp_sellsy_admin.css', __FILE__ ), array(), '1.0', 'screen' );
				wp_enqueue_style( 'wpsellsystylesadmin' );
			}

		}

		 function wpi_adm_pages_callback() {

			add_menu_page('WP Sellsy', 'WP Sellsy', 'manage_options', 'wpi-admPage', array( $this, 'wpi_admPage'), plugins_url( '/img/sellsy_15.png', __FILE__ ) );

		}

		function wpi_admPage() {

			if ( is_admin() AND current_user_can( 'manage_options' ) ) {
				include_once WPI_PATH_INC . '/wp_sellsy-adm-page.php';
			}

		}

		function wpi_loadLang() {

			load_plugin_textdomain( 'wpsellsy', true, WPI_PATH_LANG );

		}

		function wpi_on_deactivate_callback() {

			delete_option( 'wpsellsy_options' );

		}

		function wpi_register_settings() {

			require_once WPI_PATH . '/wp_sellsy-settings.class.php';
			
			new wp_sellsySettings();

		}

		public function wpi_sellsy_options( $option ) {
			// En gros le plugin n'a jamais du marché à cause de cette ligne !!!
			//if ( current_user_can( 'manage_options' ) ) {
				$options = get_option( 'wpsellsy_options' );
				if ( isset( $options[$option] ) )
					return $options[$option];
				else
					return false;
			//}
		}

		public function wpi_checkSellsy_connect() {

			if ( is_admin() AND current_user_can( 'manage_options' ) ) {
				sellsyTools::storageSet( 'infos', sellsyConnect_curl::load()->getInfos() );
				if ( isset( $_SESSION['oauth_error'] ) AND $_SESSION['oauth_error'] != '' )
					return false;
				else
					return true;
			} else {
				wp_die( __( 'Vous n\'avez pas les droits suffisants pour accéder à cette page.', 'wpsellsy' ) );
			}

		}

		public function wpi_checkOppSource( $sourceParam ) {

			if ( is_admin() AND current_user_can( 'manage_options' ) ) {
				$request = array(
					'method' => 'Opportunities.getSources', 
					'params' => array()
				);
				$sources = sellsyConnect_curl::load()->requestApi( $request );
				$sourceX = null;
				foreach ( $sources->response AS $source ) {
					if ( is_object( $source ) AND strcasecmp( $source->label, $sourceParam ) == 0 ){
						$sourceX = $source->id;
						break;
					}
				}
				if ( $sourceX == null )
					return false;
				else
					return true;
			} else {
				wp_die( __( 'Vous n\'avez pas les droits suffisants pour accéder à cette page.', 'wpsellsy' ) );
			}

		}

		function wpi_shortcode() {

			add_shortcode( 'wpsellsy', array( $this, 'wpi_shortcode_body' ) );

		}

		function wpi_shortcode_body( $attr, $content = null ) {

			include_once WPI_PATH_INC . '/wp_sellsy-pub-page.php';

		}

		function wpi_widget() {

			include_once WPI_PATH_INC . '/wp_sellsy-widget.class.php';
			
		}

		function wpi_check_cURL(){

			if  ( !in_array( 'curl', get_loaded_extensions() ) ) {
				echo '<div class="error"><p>';
				__( 'L\'extension PHP cURL n\'est pas installée ou activée sur votre hébergement. Vous ne pouvez pas utiliser le plugin WP Sellsy Prospection.', 'wpsellsy' );
				echo '</p></div>';
			}

		}

		function wpi_form_validate() {

			$options = get_option( 'wpsellsy_options' );
			$txtVal = array( 
				'WPIraisonsociale' => __( 'de la raison sociale', 'wpsellsy'),
				'WPIsiteweb' => __( 'du site internet', 'wpsellsy'),
				'WPInomcont' => __( 'du nom du contact', 'wpsellsy'),
				'WPIprenomcont' => __( 'du prénom du contact', 'wpsellsy'),
				'WPIfonccont' => __( 'de la fonction du contact', 'wpsellsy'),
				'WPItel' => __( 'du téléphone', 'wpsellsy'),
				'WPIport' => __( 'du portable', 'wpsellsy'),
				'WPIemail' => __( 'de l\'email', 'wpsellsy'),
				'WPIfax' => __( 'du fax', 'wpsellsy'),
				'WPInote' => __( 'de la note', 'wpsellsy')
			);
			if ( isset( $options['WPIjsValid'] ) AND $options['WPIjsValid'] == 'choice1' ) {
			?>
				<script type="text/javascript">
					//<![CDATA[
					jQuery( document ).ready( function( $ ) {
						$( '#wp-sellsy-form' ).validate({
							rules: {
								<?php
									foreach( $options AS $key => $value ) {

										if ( $value == 'choice3' ) {
											switch ( $key ) {
												case 'WPIsiteweb':
													echo 'WPIsiteweb: { required: true, url: true }, ';
													break;
												case 'WPIemail':
													echo 'WPIemail: { required: true, email: true }, ';
													break;
												default:
													echo $key . ': { required: true, minlength: 3 }, ';
											}
										}

									}
								?>
							},
							messages: {
								<?php

									foreach( $options AS $key => $value ) {

										if ( $value == 'choice3' ) {
											echo $key . ': "' . __ ( 'Merci de vérifier la saisie ', 'wpsellsy' ) . $txtVal[$key] . '", ';
										}

									}

								?>
							}
						});
					});
					//]]>
					</script>
			<?php
			}

		}

		function wpi_createOppSource() {

			$nonce = $_POST['nonce'];

			if ( !wp_verify_nonce( $nonce, 'wpi_ajax_nonce' ) )
				die ( __( 'Accès interdit', 'wpsellsy' ) );

			if ( isset( $_POST['action'] ) AND $_POST['action'] == 'wpi_createOppSource'
				AND isset( $_POST['param'] ) AND $_POST['param'] == 'creerSource' ) {

				$options = get_option( 'wpsellsy_options' );
				$source = $options['WPInom_opp_source'];

				$request = array(
					'method' => 'Opportunities.createSource', 
					'params' => array(
						'source'	=> array(
							'label'		=> $source
						)
					)
				);

				$creersource = sellsyConnect_curl::load()->requestApi( $request );
				if ( $creersource->response != '' )
					echo 'true';
				else
					echo 'false';
				die();
			}

		}

		function wpi_restrict_admin(){

			if ( !current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Vous n\'avez pas les droits suffisants pour accéder à cette page.', 'wpsellsy' ) );
			}

		}

		function wpi_pointers_styles( $hook_suffix ) {

			$wp_sellsyScriptStyles = false;
			$dismissed_pointers = explode( ',', get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

			if( ! in_array( 'wpi_pointer', $dismissed_pointers ) ) {
				$wp_sellsyScriptStyles = true;
				add_action( 'admin_print_footer_scripts', array( $this, 'wpi_pointers_scripts' ) );
			}

			if( $wp_sellsyScriptStyles ) {
				wp_enqueue_style( 'wp-pointer' );
				wp_enqueue_script( 'wp-pointer' );
			}

		}

		function wpi_pointers_scripts() {

			$pointer_content  = '<h3>WP Sellsy Prospection</h3>';
			$pointer_content .= '<p><img src="../wp-content/plugins/wp_sellsy/img/sellsy_34.png" alt="" style="float:left;margin-right:10px" /> ' . __( 'Vous avez installé le plugin WP Sellsy Prospection. Cliquez ici pour procéder à sa configuration', 'wpsellsy' ) . '</p>';
			?>
			<script type="text/javascript">
				//<![CDATA[
				jQuery(document).ready( function($) {
					$( '#toplevel_page_wpi-admPage' ).pointer({
						content:	'<?php echo $pointer_content; ?>',
						position: 	{
							edge:	'left',
							align:	'right'
						},
						pointerWidth:	350,
						close:			function() {
							$.post( ajaxurl, {
							pointer: 'wpi_pointer',
							action: 'dismiss-wp-pointer'
							});
						}
					}).pointer('open');
				});
				//]]>
			</script>

			<?php

		}

	}

}
if ( class_exists( 'wp_sellsyClass' ) ) {
	$wp_sellsy = new wp_sellsyClass();
}