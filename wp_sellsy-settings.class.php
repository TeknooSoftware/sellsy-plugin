<?php
require_once WPI_PATH_INC . '/sellsyconnect_curl.php';
require_once WPI_PATH_INC . '/sellsytools.php';

class wp_sellsySettings {

	private $settings;
	private $sections;
	private $checkboxes;

	public function __construct() {

		$this->settings = array();
		$this->checkboxes = array();
		$this->wpiGet_settings();

		$this->sections['sellsy_connexion']	= __( 'Connexion à votre compte Sellsy', 'wpsellsy' );
		$this->sections['sellsy_options']		= __( 'Options du plugin', 'wpsellsy' );
		$this->sections['sellsy_loadjQuery']	= __( 'Charger le framework jQuery du plugin (' . WPI_JQUERY_VERSION . ')', 'wpsellsy' );
		$this->sections['sellsy_jsValid']		= __( 'Validation Javascript (requiert jQuery)', 'wpsellsy' );
		$this->sections['sellsy_Champs']		= __( 'Afficher / Masquer les champs', 'wpsellsy' );

		add_action( 'admin_init', array( &$this, 'wpiRegister_settings' ) );

		if ( !get_option( 'wpsellsy_options' ) )
			$this->wpiInitialize_settings();

	}

	public function wpiCreate_setting( $args = array() ) {

		$defaults = array(
			'id'		=> 'champ_defaut',
			'title'		=> 'Champ par défaut',
			'desc'		=> __('Description par défaut', 'wpsellsy'),
			'std'		=> '',
			'type'		=> 'text',
			'section'	=> 'sellsy_connexion',
			'choices'	=> array(),
			'class'		=> ''
		);

		extract( wp_parse_args( $args, $defaults ) );

		$field_args = array(
			'type'      => $type,
			'id'        => $id,
			'desc'      => $desc,
			'std'       => $std,
			'choices'   => $choices,
			'label_for' => $id,
			'class'     => $class
		);

		if ( $type == 'checkbox' )
			$this->checkboxes[] = $id;

		add_settings_field( $id, $title, array( $this, 'wpiDisplay_setting' ), 'wpi-admPage', $section, $field_args );

	}


	public function wpiDisplay_section() {

	}

	public function wpiDisplay_setting( $args = array() ) {

		extract( $args );
		
		$options = get_option( 'wpsellsy_options' );
		
		if ( !isset( $options[$id] ) AND $type != 'checkbox' )
			$options[$id] = $std;
		elseif ( !isset( $options[$id] ) )
			$options[$id] = 0;
		
		$field_class = '';
		if ( $class != '' )
			$field_class = ' ' . $class;
		
		switch ( $type ) {
				
			case 'select':
				echo '<select class="select' . $field_class . '" name="wpsellsy_options[' . $id . ']">';

				foreach ( $choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $options[$id], $value, false ) . '>' . $label . '</option>';

				echo '</select>';

				if ( $desc != '' )
					echo '<br><span class="description">' . $desc . '</span>';

				break;
		
			case 'radio':
				$i = 0;
				foreach ( $choices as $value => $label ) {
					echo '<input class="radio' . $field_class . '" type="radio" name="wpsellsy_options[' . $id . ']" id="' . $id . $i . '" value="' . esc_attr( $value ) . '" ' . checked( $options[$id], $value, false ) . '> <label for="' . $id . $i . '">' . $label . '</label>';
					if ( $i < count( $options ) - 1 )
						echo '<br />';
					$i++;
				}
				
				if ( $desc != '' )
					echo '<span class="description">' . $desc . '</span>';
				
				break;
			
			case 'text':
			default:
		 		echo '<input class="regular-text' . $field_class . '" type="text" id="' . $id . '" name="wpsellsy_options[' . $id . ']" placeholder="' . $std . '" value="' . esc_attr( $options[$id] ) . '" />';
		 		
		 		if ( $desc != '' )
		 			echo '<br><span class="description">' . $desc . '</span>';
		 		
		 		break;
		 	
		}

	}

	public function wpiGet_settings() {

		/* Section Connexion Sellsy */

		$this->settings['WPIconsumer_token'] = array(
			'title'   => __( 'Consumer Token', 'wpsellsy' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'sellsy_connexion'
		);

		$this->settings['WPIconsumer_secret'] = array(
			'title'   => __( 'Consumer Secret', 'wpsellsy' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'sellsy_connexion'
		);

		$this->settings['WPIutilisateur_token'] = array(
			'title'   => __( 'Utilisateur Token', 'wpsellsy' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'sellsy_connexion'
		);

		$this->settings['WPIutilisateur_secret'] = array(
			'title'   => __( 'Utilisateur Secret', 'wpsellsy' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'sellsy_connexion'
		);

		/* Section Options du plugin */

		$this->settings['WPIcreer_prospopp'] = array(
			'title'   => __( 'Créer', 'wpsellsy' ),
			'desc'    => '',
			'type'    => 'radio',
			'std'     => '',
			'section' => 'sellsy_options',
			'choices' => array(
				'choice1' => __('Un prospect seulement', 'wpsellsy'),
				'choice2' => __('Un prospect et une opportunité', 'wpsellsy')
			)
		);

		$this->settings['WPIenvoyer_copie'] = array(
			'title'   => __( 'Envoyer une copie à', 'wpsellsy' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'sellsy_options'
		);

		$this->settings['WPInom_opp_source'] = array(
			'title'   => __( 'Nom de la source pour les opportunités', 'wpsellsy' ),
			'desc'    => __( 'Vous devez renseigner ce champ si vous souhaitez créer une opportunité en plus d\'un prospect. La source doit exister sur votre compte <a href="https://www.sellsy.com/?_f=prospection_prefs&action=sources" target="_blank">Sellsy.com</a>.' , 'wpsellsy' ),
			'std'     => '',
			'type'    => 'text',
			'section' => 'sellsy_options'
		);

		$this->settings['WPInom_form'] = array(
			'title'   => __( 'Nom du formulaire', 'wpsellsy' ),
			'desc'    => '',
			'std'     => '',
			'type'    => 'text',
			'section' => 'sellsy_options'
		);

		$this->settings['WPIaff_form'] = array(
			'title'   => __( 'Afficher le nom du formulaire', 'wpsellsy' ),
			'desc'    => __( 'Vous permet d\'afficher ou de masquer le nom du formulaire inclus dans vos pages et/ou articles via le shortcode.', 'wpsellsy' ),
			'type'    => 'radio',
			'std'     => '',
			'section' => 'sellsy_options',
			'choices' => array(
				'choice1' => __( 'Oui', 'wpsellsy'),
				'choice2' => __( 'Non', 'wpsellsy' )
			)
		);
		
		/* Section Charger jQuery */

		$this->settings['WPIloadjQuery'] = array(
			'title'   => __( 'Activer', 'wpsellsy' ),
			'desc'    => __( 'Désactive la version de jQuery incluse dans votre thème (si il y en a une) et intègre la version 1.9.1 du CDN Google.', 'wpsellsy' ),
			'type'    => 'radio',
			'std'     => '',
			'section' => 'sellsy_loadjQuery',
			'choices' => array(
				'choice1' => __( 'Oui', 'wpsellsy' ),
				'choice2' => __( 'Non', 'wpsellsy' )
			)
		);

		/* Section Activer validation JS */

		$this->settings['WPIjsValid'] = array(
			'title'   => __( 'Activer', 'wpsellsy' ),
			'desc'    => __( 'La validation Javascript permet de vérifier les informations saisies avant que le formulaire soit soumis au serveur (sans rafraîchissement de la page).', 'wpsellsy' ),
			'type'    => 'radio',
			'std'     => '',
			'section' => 'sellsy_jsValid',
			'choices' => array(
				'choice1' => __( 'Oui', 'wpsellsy' ),
				'choice2' => __( 'Non', 'wpsellsy' )
			)
		);

		/* Section Afficher / Masquer les champs */

		$this->settings['WPIraisonsociale'] = array(
			'section' => 'sellsy_Champs',
			'title'   => __( 'Raison sociale', 'wpsellsy'),
			'desc'    => __( 'Zone de saisie de la raison sociale du prospect', 'wpsellsy'),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => __( 'Ne pas afficher', 'wpsellsy' ),
				'choice2' => __( 'Afficher seulement', 'wpsellsy' ),
				'choice3' => __( 'Afficher et forcer la saisie', 'wpsellsy' ),
			)
		);

		$this->settings['WPIsiteweb'] = array(
			'section' => 'sellsy_Champs',
			'title'   => __( 'Site internet', 'wpsellsy'),
			'desc'    => __( 'Zone de saisie du site internet du prospect', 'wpsellsy'),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => __( 'Ne pas afficher', 'wpsellsy' ),
				'choice2' => __( 'Afficher seulement', 'wpsellsy' ),
				'choice3' => __( 'Afficher et forcer la saisie', 'wpsellsy' ),
			)
		);

		$this->settings['WPInomcont'] = array(
			'section' => 'sellsy_Champs',
			'title'   => __( 'Nom du contact', 'wpsellsy'),
			'desc'    => __( 'Zone de saisie du nom de la personne à contacter', 'wpsellsy'),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => __( 'Ne pas afficher', 'wpsellsy' ),
				'choice2' => __( 'Afficher seulement', 'wpsellsy' ),
				'choice3' => __( 'Afficher et forcer la saisie', 'wpsellsy' ),
			)
		);

		$this->settings['WPIprenomcont'] = array(
			'section' => 'sellsy_Champs',
			'title'   => __( 'Prénom du contact', 'wpsellsy'),
			'desc'    => __( 'Zone de saisie du prénom de la personne à contacter', 'wpsellsy'),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => __( 'Ne pas afficher', 'wpsellsy' ),
				'choice2' => __( 'Afficher seulement', 'wpsellsy' ),
				'choice3' => __( 'Afficher et forcer la saisie', 'wpsellsy' ),
			)
		);

		$this->settings['WPIfonccont'] = array(
			'section' => 'sellsy_Champs',
			'title'   => __( 'Fonction du contact', 'wpsellsy'),
			'desc'    => __( 'Zone de saisie de la fonction de la personne à contacter', 'wpsellsy'),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => __( 'Ne pas afficher', 'wpsellsy' ),
				'choice2' => __( 'Afficher seulement', 'wpsellsy' ),
				'choice3' => __( 'Afficher et forcer la saisie', 'wpsellsy' ),
			)
		);

		$this->settings['WPItel'] = array(
			'section' => 'sellsy_Champs',
			'title'   => __( 'Téléphone du prospect', 'wpsellsy'),
			'desc'    => __( 'Zone de saisie du numéro de téléphone fixe du prospect', 'wpsellsy'),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => __( 'Ne pas afficher', 'wpsellsy' ),
				'choice2' => __( 'Afficher seulement', 'wpsellsy' ),
				'choice3' => __( 'Afficher et forcer la saisie', 'wpsellsy' ),
			)
		);

		$this->settings['WPIport'] = array(
			'section' => 'sellsy_Champs',
			'title'   => __( 'Portable du prospect', 'wpsellsy'),
			'desc'    => __( 'Zone de saisie du numéro de téléphone portable du prospect', 'wpsellsy'),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => __( 'Ne pas afficher', 'wpsellsy' ),
				'choice2' => __( 'Afficher seulement', 'wpsellsy' ),
				'choice3' => __( 'Afficher et forcer la saisie', 'wpsellsy' ),
			)
		);

		$this->settings['WPIemail'] = array(
			'section' => 'sellsy_Champs',
			'title'   => __( 'Email du prospect', 'wpsellsy'),
			'desc'    => __( 'Zone de saisie de l\'adresse email du prospect (saisie obligatoire si affiché)' , 'wpsellsy'),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => __( 'Ne pas afficher', 'wpsellsy' ),
				'choice3' => __( 'Afficher et forcer la saisie', 'wpsellsy' ),
			)
		);

		$this->settings['WPIfax'] = array(
			'section' => 'sellsy_Champs',
			'title'   => __( 'Fax du prospect', 'wpsellsy'),
			'desc'    => __( 'Zone de saisie du numéro de fax du prospect', 'wpsellsy'),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => __( 'Ne pas afficher', 'wpsellsy' ),
				'choice2' => __( 'Afficher seulement', 'wpsellsy' ),
				'choice3' => __( 'Afficher et forcer la saisie', 'wpsellsy' ),
			)
		);

		$this->settings['WPInote'] = array(
			'section' => 'sellsy_Champs',
			'title'   => __( 'Brief / Note', 'wpsellsy'),
			'desc'    => __( 'Zone de saisie du la note ou du brief', 'wpsellsy'),
			'type'    => 'select',
			'std'     => '',
			'choices' => array(
				'choice1' => __( 'Ne pas afficher', 'wpsellsy' ),
				'choice2' => __( 'Afficher seulement', 'wpsellsy' ),
				'choice3' => __( 'Afficher et forcer la saisie', 'wpsellsy' ),
			)
		);

	}

	public function wpiInitialize_settings() {

		$default_settings = array();

		foreach ( $this->settings AS $id => $setting ) {
			if ( $setting['type'] != 'heading' )
				$default_settings[$id] = $setting['std'];
		}
		
		update_option( 'wpsellsy_options', $default_settings );

	}

	public function wpiRegister_settings() {

		register_setting( 'wpsellsy_options', 'wpsellsy_options', array( &$this, 'wpiSanitize_settings' ) );

		foreach ( $this->sections AS $slug => $title ) {
			add_settings_section( $slug, $title, array( &$this, 'wpiDisplay_section' ), 'wpi-admPage' );
		}

		$this->wpiGet_settings();
		
		foreach ( $this->settings AS $id => $setting ) {
			$setting['id'] = $id;
			$this->wpiCreate_setting( $setting );
		}

	}

	public function wpiSanitize_settings( $input ) {

		if ( current_user_can( 'manage_options' ) AND check_admin_referer( 'wpi_nonce_field', 'wpi_nonce_verify_adm' ) ) {

			$output = array();

			foreach( $input AS $key => $value ) {
				if( isset( $input[$key] ) ) {
					$output[$key] = strip_tags( stripslashes( $input[ $key ] ) );
				}
			}

			function wpiValidate_settings( $output, $input ) {
				
				foreach ( $input AS $key => $val ) {

					switch ( $key ){

						case 'WPIconsumer_token':
							if ( strlen( $val ) != 40 )
								add_settings_error( 'wpsellsy_options', 'WPIconsumer_token', __( 'Le Consumer Token est manquant ou incorrect, vérifiez votre saisie.', 'wpsellsy' ) , 'error' );
							else
								$output[$key] = sanitize_text_field( $input[ $key ] );
							break;

						case 'WPIconsumer_secret':
							if ( strlen( $val ) != 40 )
								add_settings_error( 'wpsellsy_options', 'WPIconsumer_secret', __( 'Le Consumer Secret est manquant ou incorrect, vérifiez votre saisie.', 'wpsellsy' ) , 'error' );
							else
								$output[$key] = sanitize_text_field( $input[ $key ] );
							break;

						case 'WPIutilisateur_token':
							if ( strlen( $val ) != 40 )
								add_settings_error( 'wpsellsy_options', 'WPIutilisateur_token', __( 'L\'Utilisateur Token est manquant ou incorrect, vérifiez votre saisie.', 'wpsellsy' ) , 'error' );
							else
								$output[$key] = sanitize_text_field( $input[ $key ] );
							break;

						case 'WPIutilisateur_secret':
							if ( strlen( $val ) != 40 )
								add_settings_error( 'wpsellsy_options', 'WPIutilisateur_secret', __( 'L\'Utilisateur Secret est manquant ou incorrect, vérifiez votre saisie.', 'wpsellsy' ) , 'error' );
							else
								$output[$key] = sanitize_text_field( $input[ $key ] );
							break;

						case 'WPIenvoyer_copie':
							if ( $val == '' OR !is_email( $val ) )
								add_settings_error( 'wpsellsy_options', 'WPIenvoyer_copie', __( 'L\'adresse email est manquante ou incorrecte, vérifiez votre saisie ou vous ne recevrez pas d\'email à chaque soumission du formulaire. Les prospects et/ou opportunités seront quand-même créé(e)s.', 'wpsellsy' ) , 'error' );
							else
								$output[$key] = sanitize_text_field( $input[ $key ] );
							break;

						case 'WPIcreer_prospopp':
							if ( $val == 'choice2' AND $input['WPInom_opp_source'] == '' )
								add_settings_error( 'wpsellsy_options', 'WPInom_opp_source', __( 'Vous avez choisi de créer une opportunité en plus d\'un prospect, vous devez saisir une source d\'opportunités ou le plugin créera que des prospects.', 'wpsellsy' ) , 'error' );
							$output[$key] = sanitize_text_field( $input[ $key ] );
							break;

						case 'WPIaff_form':
							if ( $val == 'choice1' AND $input['WPInom_form'] == '' )
								add_settings_error( 'wpsellsy_options', 'WPInom_opp_source', __( 'Vous avez choisi d\'afficher le nom du formulaire mais vous ne l\'avez pas renseigné.', 'wpsellsy' ) , 'error' );
							break;
					}

				}

				return $output;

			}

			add_filter( 'wpiSanitize_settings', 'wpiValidate_settings', 10, 2 );
			return apply_filters( 'wpiSanitize_settings', $output, $input );

		} 
		
	}

}
?>