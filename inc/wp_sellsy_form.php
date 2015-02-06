<p>
    <label for="<?php echo $this->get_field_id('titre'); ?>">
        <?php _e('Titre:', 'wpsellsy'); ?>
    </label>
    <input class="widefat" id="<?php echo $this->get_field_id('titre'); ?>" name="<?php echo $this->get_field_name('titre'); ?>" type="text" value="<?php echo $titre; ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id('texte'); ?>">
        <?php _e('Description:', 'wpsellsy'); ?>
    </label>
    <input class="widefat" id="<?php echo $this->get_field_id('texte'); ?>" name="<?php echo $this->get_field_name('texte'); ?>" type="text" value="<?php echo $texte; ?>" />
</p>