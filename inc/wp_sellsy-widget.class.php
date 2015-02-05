<?php

class WPI_Widget extends WP_Widget {

	public function __construct() {

		$this->WP_Widget(
			'wpi_widget',
			__( 'WP Sellsy', 'wpsellsy' ),
			array( 'classname' => 'wpi_widget_single', 'description' => __( 'Affiche le widget WP Sellsy', 'wpsellsy' ) ),
			array( )	
		);

	}

	public function widget ( $args, $instance ) {

		extract( $args );

		$titre = apply_filters( 'widget_title', $instance['titre'] ) ;

		$out = isset( $instance['texte'] ) ? '<p>'. $instance['texte'] . '</p>' : '';
	
		if ( !empty( $out ) ) {
			echo $before_widget;
			if ( $titre ) {
				echo $before_title . $titre . $after_title;
			}
			?>
				<div>
					<?php 
					echo $out;
					include_once WPI_PATH_INC . '/wp_sellsy-pub-page.php';
					?>

				</div>
			<?php
				echo $after_widget;
		}

	}

	public function update ( $new_instance, $old_instance ) {

		$instance = $old_instance;
		
		$instance['titre'] = strip_tags( $new_instance['titre'] );
		$instance['texte'] = strip_tags( $new_instance['texte'] );

		return $instance;

	}

	public function form ( $instance ) {

		
		$titre = ( isset( $instance['titre'] ) ) ? esc_attr( $instance[ 'titre' ] ) : '';
		$texte = ( isset( $instance['titre'] ) ) ? esc_attr( $instance[ 'texte' ] ) : '';

		?>
		<p><label for="<?php echo $this->get_field_id('titre'); ?>"><?php _e( 'Titre:', 'wpsellsy'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('titre'); ?>" name="<?php echo $this->get_field_name('titre'); ?>" type="text" value="<?php echo $titre; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('texte'); ?>"><?php _e( 'Description:', 'wpsellsy'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('texte'); ?>" name="<?php echo $this->get_field_name('texte'); ?>" type="text" value="<?php echo $texte; ?>" /></p>
	<?php
	}
}

register_widget( 'WPI_Widget' );

?>