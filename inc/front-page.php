<?php
require_once SELLSY_WP_PATH_INC . '/fonctions.php';

if ( !is_admin() AND isset( $_POST['send_wp_sellsy'] ) ) {
	if ( wp_verify_nonce( $_POST['slswp_nonce_verify_page'], 'slswp_nonce_field' ) ) {
		
		$options = get_option( 'wpsellsy_options' );
		
		$request_prosp = array(
			'method' => 'Prospects.create'
		);

		$email = '';
		$validation = array();
		$nom_form = $options['WPInom_form'];

		foreach ( $options AS $key => $value ) {
			switch ( $key ) {
				case 'WPIraisonsociale':
					if ( isset( $_POST['WPIraisonsociale'] ) AND $_POST['WPIraisonsociale'] != '' ) {
						$WPIraisonsociale = sanitize_text_field( stripslashes( $_POST['WPIraisonsociale'] ) );
						$email .= 'Raison sociale: ' . $WPIraisonsociale .'<br>';
						$request_prosp['params']['third']['name'] = $WPIraisonsociale;
					} else {
						if( $value == 'choice3' )
							$validation[] = 'WPIraisonsociale';
					}
					break;
				case 'WPIsiteweb':
					if ( isset( $_POST['WPIsiteweb'] ) AND $_POST['WPIsiteweb'] != '' AND validateURL( $_POST['WPIsiteweb'] ) ) {
						$WPIsiteweb = esc_url( $_POST['WPIsiteweb'] );
						$email .= 'Site internet: ' . $WPIsiteweb .'<br>';
						$request_prosp['params']['third']['web'] = $WPIsiteweb;
					} else {
						if( $value == 'choice3' )
							$validation[] = 'WPIsiteweb';
					}
					break;
				case 'WPInomcont':
					if ( isset ( $_POST['WPInomcont'] ) AND $_POST['WPInomcont'] != '' ) {
						$WPInomcont = sanitize_text_field( stripslashes( $_POST['WPInomcont'] ) );
						$email .= 'Nom du contact: ' . $WPInomcont .'<br>';
						$request_prosp['params']['contact']['name'] = $WPInomcont;
					} else {
						if( $value == 'choice3' )
							$validation[] = 'WPInomcont';
					}
					break;
				case 'WPIprenomcont':
					if ( isset( $_POST['WPIprenomcont']  ) AND $_POST['WPIprenomcont'] != '' ) {
						$WPIprenomcont = sanitize_text_field( stripslashes( $_POST['WPIprenomcont'] ) );
						$email .= 'Prénom du contact: ' . $WPIprenomcont .'<br>';
						$request_prosp['params']['contact']['forename'] = $WPIprenomcont;
					} else {
						if( $value == 'choice3' )
							$validation[] = 'WPIprenomcont';
					}
					break;
				case 'WPIfonccont':
					if ( isset( $_POST['WPIfonccont'] ) AND $_POST['WPIfonccont'] != '' ) {
						$WPIfonccont = sanitize_text_field( stripslashes( $_POST['WPIfonccont'] ) );
						$email .= 'Fonction du contact: ' . $WPIfonccont .'<br>';
						$request_prosp['params']['contact']['position'] = $WPIfonccont;
					} else {
						if( $value == 'choice3' )
							$validation[] = 'WPIfonccont';
					}
					break;
				case 'WPItel':
					if ( isset( $_POST['WPItel'] ) AND $_POST['WPItel'] != '' ) {
						$WPItel = sanitize_text_field( stripslashes( $_POST['WPItel'] ) );
						$email .= 'Téléphone fixe: ' . $WPItel .'<br>';
						$request_prosp['params']['contact']['tel'] = $WPItel;
					} else {
						if( $value == 'choice3' )
							$validation[] = 'WPItel';
					}
					break;
				case 'WPIport':
					if ( isset( $_POST['WPIport'] ) AND $_POST['WPIport'] != '' ) {
						$WPIport = sanitize_text_field( stripslashes( $_POST['WPIport'] ) );
						$email .= 'Téléphone portable: ' . $WPIport .'<br>';
						$request_prosp['params']['contact']['mobile'] = $WPIport;
					} else {
						if( $value == 'choice3' )
							$validation[] = 'WPIport';
					}
					break;
				case 'WPIemail':
					if ( isset( $_POST['WPIemail'] ) AND $_POST['WPIemail'] != '' AND is_email( $_POST['WPIemail'] ) ) {
						$WPIemail = sanitize_text_field( stripslashes( $_POST['WPIemail'] ) );
						$email .= 'Email: ' . $WPIemail .'<br>';
						$request_prosp['params']['contact']['email'] = $WPIemail;
					} else {
						if( $value == 'choice3' OR $value == 'choice2' )
							$validation[] = 'WPIemail';
					}
					break;
				case 'WPIfax':
					if ( isset( $_POST['WPIfax'] ) AND $_POST['WPIfax'] != '' ) {
						$WPIfax = sanitize_text_field( stripslashes( $_POST['WPIfax'] ) );
						$email .= 'Fax: ' . $WPIfax .'<br>';
						$request_prosp['params']['contact']['fax'] = $WPIfax;
					} else {
						if ( $value == 'choice3' )
							$validation[] = 'WPIfax';
					}
					break;
				case 'WPInote':
					if ( isset( $_POST['WPInote'] ) AND $_POST['WPInote'] != '' ) {
						$WPInote = sanitize_text_field( stripslashes( $_POST['WPInote'] ) );
						$email .= 'Note opportunité: ' . $WPInote .'<br>';
						//$request_prosp['params']['contact']['fax'] = $WPInote;
					} else {
						if( $value == 'choice3' )
							$validation[] = 'WPInote';
					}
					break;

			}
		}

		if ( empty( $validation ) ) {

			if ( is_email( $options['WPIenvoyer_copie'] ) AND class_exists( 'PHPMailer' ) ) {

				$domain = preg_replace( '/^www\./','',$_SERVER['SERVER_NAME'] );
				$mail = new PHPMailer();
				$mail->SetFrom( 'sellsy-form@' . $domain, 'Formulaire Sellsy' );
				$mail->AddAddress( $options['WPIenvoyer_copie'] );
				$mail->Subject = ( $options['WPInom_form'] != '' ) ? $options['WPInom_form'] : __( 'Formulaire site web: Demande d\'informations', 'wpsellsy' );
				$mail->MsgHTML( $email );
				$mail->CharSet="UTF-8";

				if ( $mail->Send() ) {
					echo '<p><span style="color:#13D326">' . __( 'Votre message a bien été envoyé.', 'wpsellsy' ) . '</span></p>';
				} else {
					echo '<p><span style="color:#D31613">' . __( 'Votre message n\'a pas été envoyé.', 'wpsellsy' ) . '</span></p>';
				}
			}
			if ( count( $request_prosp ) > 1 ) {
				
				sellsyConnect_curl::load()->checkApi();

				$createProsp = sellsyConnect_curl::load()->requestApi( $request_prosp );
				$idProspect = $createProsp->response;

				function getCurrentIdent() {
					$request = array(
						'method' => 'Opportunities.getCurrentIdent',
						'params' => array()
					);
					$ident = sellsyConnect_curl::load()->requestApi($request);
					return $ident->response;
				}

				function getFunnels() {
					$request = array(
						'method' => 'Opportunities.getFunnels',
						'params' => array()
					);
					$funnels = sellsyConnect_curl::load()->requestApi($request);
					foreach ( $funnels->response AS $key => $funnel ) {
						if ( is_object( $funnel ) AND $funnel->name == 'défaut' ){
							$pipelineId = $funnel->id;
						} else {
							$pipelineId = $funnel;
						}
					}
					return $pipelineId;
				}

				function getStepId($funnelId) {
					$request = array(
						'method' => 'Opportunities.getStepsForFunnel', 
						'params' => array(
							'funnelid' => $funnelId
						)
					);
					$steps = sellsyConnect_curl::load()->requestApi($request);
					$i = 0;
					foreach ( $steps->response AS $key => $step ) {
						if ( $i != 1 ) {
							$stepId = $step->id;
							$i++;
						}
					}
					return $stepId;
				}

				function getSources($WPInom_opp_source) {
					$request = array(
						'method' => 'Opportunities.getSources', 
						'params' => array()
					);
					$sources = sellsyConnect_curl::load()->requestApi($request);
					foreach ( $sources->response AS $key => $source ) {
						if ( is_object( $source ) AND $source->label == $WPInom_opp_source ){
							$sourceId = $source->id;
						}
					}
					return $sourceId;
				}
				
				if ( $options['WPIcreer_prospopp'] == 'choice2' OR  $options['WPIcreer_prospopp'] == 'choice3' ) {
					$lastOpportunity = getCurrentIdent();
					$funnels = getFunnels();
					$nom_opp_source = $options['WPInom_opp_source'];
					$sourceid = getSources( $options['WPInom_opp_source'] );
					$step = getStepId( $funnels );
					$date = strtotime( "+1 week", time() );
					$request_opp = array(
						'method' => 'Opportunities.create',
						'params' => array(
							'opportunity' => array(
								'linkedtype' => 'prospect',
								'linkedid' => $idProspect,
								'ident' => $lastOpportunity,
								'sourceid' => $sourceid,
								'dueDate' => $date,
								'name' => __( 'Contact site web', 'wpsellsy' ),
								'funnelid' => $funnels,
								'stepid' => $step,
								'brief' => $WPInote
							)
						)
					);
					$result = sellsyConnect_curl::load()->requestApi( $request_opp );
				}
				unset( $WPInote, $WPIemail, $WPIfonccont, $WPItel, $WPIprenomcont, $WPInomcont, $WPIfax, $WPIsiteweb, $WPIraisonsociale, $WPIport, $WPIport );
			}
		} else {
			$erreurMsg = '<div class="formError"><span>' . __( 'Votre message n\'a pas été envoyé, vérifiez la saisie des champs suivant :', 'wpsellsy' ) . '</span><p><br />';
			foreach ( $validation AS $valeur ) {

				switch ( $valeur ) {
					case 'WPIraisonsociale':
						$erreurMsg .= __( 'Vérifiez la saisie de votre raison sociale', 'wpsellsy' ) . '<br />';
						break;
					case 'WPIsiteweb':
						$erreurMsg .= __( 'Vérifiez la saisie de votre site internet', 'wpsellsy' ) . '<br />';
						break;
					case 'WPIprenomcont':
						$erreurMsg .= __( 'Vérifiez la saisie du prénom de la personne à contacter', 'wpsellsy' ) . '<br />';
						break;
					case 'WPInomcont':
						$erreurMsg .= __( 'Vérifiez la saisie du nom de la personne à contacter', 'wpsellsy' ) . '<br />';
						break;
					case 'WPIfonccont':
						$erreurMsg .= __( 'Vérifiez la saisie de la fonction de la personne à contacter', 'wpsellsy' ) . '<br />';
						break;
					case 'WPItel':
						$erreurMsg .= __( 'Vérifiez la saisie du téléphone de la personne à contacter', 'wpsellsy' ) . '<br />';
						break;
					case 'WPIport':
						$erreurMsg .= __( 'Vérifiez la saisie du portable de la personne à contacter', 'wpsellsy' ) . '<br />';
						break;
					case 'WPIemail':
						$erreurMsg .= __( 'Vérifiez la saisie de l\'adresse email de la personne à contacter', 'wpsellsy' ) . '<br />';
						break;
					case 'WPIfax':
						$erreurMsg .= __( 'Vérifiez la saisie du fax de la personne à contacter', 'wpsellsy' ) . '<br />';
						break;
					case 'WPINote':
						$erreurMsg .= __( 'Vérifiez la saisie de votre note', 'wpsellsy' ) . '<br />';
						break;
				}

			}
			$erreurMsg .= '</p></div>';
			echo $erreurMsg;
		}
	}
}
if ( !is_admin() ) {
	$options = get_option( 'wpsellsy_options' );
	if (
		$options['WPIraisonsociale'] != 'choice1' OR
		$options['WPIsiteweb'] != 'choice1' OR
		$options['WPIprenomcont'] != 'choice1' OR
		$options['WPInomcont'] != 'choice1' OR
		$options['WPIfonccont'] != 'choice1' OR
		$options['WPItel'] != 'choice1' OR
		$options['WPIemail'] != 'choice1' OR
		$options['WPIfax'] != 'choice1' OR
		$options['WPInote'] != 'choice1' OR
		$options['WPIport'] != 'choice1'
	 ) {

	if ( isset( $options['WPIaff_form'] ) AND $options['WPIaff_form'] == 'choice1' AND $options['WPInom_form'] != '' ) {
		echo '<h3>' . $options['WPInom_form'] . '</h3>';
	}

	?>
	<form method="post" action="" id="wp-sellsy-form">
		<?php if ( $options['WPIraisonsociale'] != 'choice1' ) { ?>
			<label for="WPIraisonsociale"><?php _e( 'Raison sociale', 'wpsellsy' ) ?></label>
			<input type="text" name="WPIraisonsociale" id="WPIraisonsociale" value="<?php echo ( isset( $WPIraisonsociale ) ) ? $WPIraisonsociale : ''; ?>" /><br />
		<?php }
		if ( $options['WPIsiteweb'] != 'choice1' ) { ?>
			<label for="WPIsiteweb"><?php _e( 'Site Internet', 'wpsellsy' ) ?></label>
			<input type="text" name="WPIsiteweb" id="WPIsiteweb" value="<?php echo ( isset( $WPIsiteweb ) ) ? $WPIsiteweb : ''; ?>" /><br />
		<?php  }
		if ( $options['WPIprenomcont'] != 'choice1' ) { ?>
			<label for="WPIprenomcont"><?php _e( 'Prénom du contact', 'wpsellsy' ) ?></label>
			<input type="text" name="WPIprenomcont" id="WPIprenomcont" value="<?php echo ( isset( $WPIprenomcont ) ) ? $WPIprenomcont : ''; ?>" /><br />
		<?php }
		if ( $options['WPInomcont'] != 'choice1' ) { ?>
			<label for="WPInomcont"><?php _e( 'Nom du contact', 'wpsellsy' ) ?></label>
			<input type="text" name="WPInomcont" id="WPInomcont" value="<?php echo ( isset( $WPInomcont ) ) ? $WPInomcont : ''; ?>" /><br />
		<?php }
		if ( $options['WPIfonccont'] != 'choice1' ) { ?>
			<label for="WPIfonccont"><?php _e( 'Fonction du contact', 'wpsellsy' ) ?></label>
			<input type="text" name="WPIfonccont" id="WPIfonccont" value="<?php echo ( isset( $WPIfonccont ) ) ? $WPIfonccont : ''; ?>" /><br />
		<?php }
		if ( $options['WPItel'] != 'choice1' ) { ?>
			<label for="WPItel"><?php _e( 'Téléphone', 'wpsellsy' ) ?></label>
			<input type="text" name="WPItel" id="WPItel" value="<?php echo ( isset( $WPItel ) ) ? $WPItel : ''; ?>" /><br />
		<?php }
		if ( $options['WPIport'] != 'choice1' ) { ?>
			<label for="WPIport"><?php _e( 'Portable', 'wpsellsy' ) ?></label>
			<input type="text" name="WPIport" id="WPIport" value="<?php echo ( isset( $WPIport ) ) ? $WPIport : ''; ?>" /><br />
		<?php }
		if ( $options['WPIemail'] != 'choice1' ) { ?>
			<label for="WPIemail"><?php _e( 'Email', 'wpsellsy' ) ?></label>
			<input type="text" name="WPIemail" id="WPIemail" value="<?php echo ( isset( $WPIemail ) ) ? $WPIemail : ''; ?>" /><br />
		<?php }
		if ( $options['WPIfax'] != 'choice1' ) { ?>
			<label for="WPIfax"><?php _e( 'Fax', 'wpsellsy' ) ?></label>
			<input type="text" name="WPIfax" id="WPIfax" value="<?php echo ( isset( $WPIfax ) ) ? $WPIfax : ''; ?>" /><br />
		<?php }
		if ( $options['WPInote'] != 'choice1' ) { ?>
			<label for="WPInote"><?php _e( 'Brief / Note', 'wpsellsy' ) ?></label>
			<textarea id="WPInote" name="WPInote" rows="5"><?php echo ( isset( $WPInote ) ) ? $WPInote : ''; ?></textarea>
		<?php } ?>
		<div class="submit">
	        <input type="submit" name="send_wp_sellsy" value="<?php _e( 'Valider', 'wpsellsy' ) ?>" />
	    </div>
		<?php 
			if ( function_exists( 'wp_nonce_field' ) ) 
				wp_nonce_field( 'slswp_nonce_field', 'slswp_nonce_verify_page' );
		?>
	</form>
<?php
	}
}
?>