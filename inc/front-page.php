<?php

use \UniAlteri\Sellsy\Wordpress\Form\Settings;

if (!is_admin()):
	if (isset($options[Settings::DISPLAY_FORM_NAME]) && 'displayTitle' == $options[Settings::DISPLAY_FORM_NAME] && !empty($options[Settings::FORM_NAME])) {
		echo '<h3>'.$options[Settings::FORM_NAME].'</h3>';
	}

	if (!empty($messageSent)): ?>
		<div class="alert alert-dismissible alert-success">
			<?php echo $this->options[Settings::MESSAGE_SENT]; ?>
		</div>
	<?php else:
		if (!empty($errors)): ?>
			<div class="formError alert alert-dismissible alert-danger">
				<span>
					<?php echo $this->options[Settings::MESSAGE_ERROR]; ?>
				</span>
				<?php if (is_array($errors)): ?>
					<ul>
						<?php
						foreach ($errors as $error) {
							echo '<li>'.$error.'</li>';
						}
						?>
					</ul>
				<?php else: ?>
					<?php echo $errors; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<form method="post" action="" id="wp-sellsy-form">
			<input type="hidden" name="formId" value="<?php echo $formId; ?>" :/>
			<?php $columnsClass = $options[Settings::COLUMNS_CLASS]; ?>
			<?php echo $options[Settings::FORM_CUSTOM_HEADER]; ?>
			<div class="<?php echo $columnsClass; ?>">
				<?php
				$splitColumns = $options[Settings::SPLIT_COLUMNS];
				if (empty($splitColumns)) {
					//To avoid errors
					$splitColumns = 1;
				}
				$countByColumn = ceil(count($formFieldsList)/$splitColumns);

				$colCounter = 0;
				foreach ($formFieldsList as $key=>$field) {
					if ((++$colCounter) >= $countByColumn):
						$colCounter = 0; ?>
						</div>
						<div class="<?php echo $columnsClass; ?>">
					<?php endif;

                    if (!$field instanceof \UniAlteri\Sellsy\Wordpress\Form\Field){
                        continue;
                    }

					$value = '';
					if (isset($_POST[$key])) {
						$value = $_POST[$key];
					}

					$required = '';
					if (isset($mandatoryFieldsList[$key]) || $field->isRequiredField()) {
						$required = ' required="required"';
					}

					$code = $field->getCode();
					echo '<div class="form-group">';
					switch ($field->getType()) {
						case 'text':
							echo '<label for="'.$key.'">'.$field->getName().'</label>';
							echo '<input class="form-control" placeholder="'.$field->getName().'"  type="text" name="'.$code.'" id="'.$code.'" value="'.$value.'"'.$required.' />';
							break;
						case 'radio':
							echo '<label class="label-group-radio">'.$field->getName().'</label>';
							$counter = 0;
							foreach ($field->getOptions() as $option) {
								$selected = '';
								if (in_array($option['value'], (array) $value)) {
									$selected = ' checked="checked"';
								}

								echo '<div class="radio">';
								echo '<label class="radio-inline label-radio" for="'.$code.$counter.'">'.$option['value'];
								echo '<input type="radio" name="'.$code.'" id="'.$code.$counter++.'" value="'.$option['value'].'"'.$required.$selected.' /></label>';
								echo '</div>';
							}
							break;
						case 'checkbox':
							echo '<label class="label-group-checkbox">'.$field->getName().'</label>';
							$counter = 0;
							foreach ($field->getOptions() as $option) {
								$selected = '';
								if (in_array($option['value'], (array) $value)) {
									$selected = ' checked="checked"';
								}

								echo '<div class="checkbox">';
								echo '<label class="radio-inline label-checkbox" for="'.$code.$counter.'">'.$option['value'];
								echo '<input type="checkbox" name="'.$code.'[]" id="'.$code.$counter++.'" value="'.$option['value'].'"'.$required.$selected.' /></label>';
								echo '</div>';
							}
							break;
						case 'boolean':
							$selected = '';
							if (!empty($value)) {
								$selected = ' checked="checked"';
							}

							echo '<div class="checkbox">';
							echo '<label for="'.$code.'">';
							echo '<input type="hidden" name="'.$code.'" value="" />';
							echo '<input type="checkbox" name="'.$code.'" id="'.$code.'" value="Y"'.$required.$selected.' />'.$field->getName().'</label>';
							echo '</div>';
							break;
						case 'select':
							echo '<label for="'.$code.'">'.$field->getName().'</label>';
							echo '<select name="'.$code.'" id="'.$code.'"'.$required.'>';
							foreach ($field->getOptions() as $option) {
								echo '<option value="'.$option['id'].'">'.$option['value'].'</option>';
							}
							echo '</select>';
							break;
					}
					echo '</div>';
				}
				?>
				<div class="submit">
					<input type="submit" class="btn btn-default" name="send_wp_sellsy" value="<?php _e('Valider', 'wpsellsy') ?>" />
				</div>
			</div>
			<?php echo $options[Settings::FORM_CUSTOM_FOOTER]; ?>
			<?php
			if (function_exists('wp_nonce_field')) {
				wp_nonce_field('slswp_nonce_field', 'slswp_nonce_verify_page');
			}
			?>
		</form>
	<?php endif;
endif;
