<?php

switch ($type) {
    case 'select':
        echo '<select class="select'.$class.'" name="'.\UniAlteri\Sellsy\Wordpress\OptionsBag::WORDPRESS_SETTINGS_NAME.'['.$id.']">';

        foreach ($choices as $value=>$label) {
            echo '<option value="'.esc_attr($value).'"'.selected($options[$id], $value, false).'>'.$label.'</option>';
        }

        echo '</select>';

        if (!empty($desc)) {
            echo '<br><span class="description">' . $desc . '</span>';
        }

        break;

    case 'multiselect':
        echo '<select class="select'.$class.' multiselect" name="'.\UniAlteri\Sellsy\Wordpress\OptionsBag::WORDPRESS_SETTINGS_NAME.'['.$id.'][]" multiple="multiple">';

        foreach ($choices as $value=>$label) {
            $isSelected = '';
            if (!empty($options[$id]) && in_array($value, (array) $options[$id])) {
                $isSelected = ' selected="selected"';
            }
            if ($label instanceof \UniAlteri\Sellsy\Wordpress\Form\Field) {
                $label = $label->getName();
                echo '<option value="'.esc_attr($value).'"'.$isSelected.'>'.$label.'</option>';
            } else {
                echo '<option value="'.esc_attr($value).'"'.$isSelected.'>'.$label.'</option>';
            }
        }

        echo '</select>';

        if (!empty($desc)) {
            echo '<br><span class="description">' . $desc . '</span>';
        }

        break;

    case 'radio':
        $i = 0;
        foreach ($choices as $value => $label) {
            echo '<input class="radio'.$class.'" type="radio" name="'.\UniAlteri\Sellsy\Wordpress\OptionsBag::WORDPRESS_SETTINGS_NAME.'['.$id.']" id="'.$id.$i.'" value="'.esc_attr($value).'" '.checked($options[$id], $value, false).'> <label for="'.$id.$i.'">'.$label.'</label>';
            if ($i < count($options) - 1) {
                echo '<br />';
            }
            $i++;
        }

        if (!empty($desc)) {
            echo '<span class="description">'.$desc.'</span>';
        }

        break;

    case 'textarea':
        echo '<textarea class="regular-text'.$class.'" cols="80" rows="7" id="'.$id.'" name="'.\UniAlteri\Sellsy\Wordpress\OptionsBag::WORDPRESS_SETTINGS_NAME.'['.$id.']" placeholder="'.$std.'">'.$options[$id].'</textarea>';

        if (!empty($desc)) {
            echo '<br><span class="description">'.$desc.'</span>';
        }
        break;

    case 'text':
    default:
        echo '<input class="regular-text'.$class.'" type="text" id="'.$id.'" name="'.\UniAlteri\Sellsy\Wordpress\OptionsBag::WORDPRESS_SETTINGS_NAME.'['.$id.']" placeholder="'.$std.'" value="'.esc_attr($options[$id]).'" />';

        if (!empty($desc)) {
            echo '<br><span class="description">'.$desc.'</span>';
        }

        break;
}
