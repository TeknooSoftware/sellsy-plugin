<?php

switch ($type) {
    case 'select':
        echo '<select class="select'.$class.'" name="wpsellsy_options['.$id.']">';

        foreach ($choices as $value=>$label) {
            echo '<option value="'.esc_attr($value).'"'.selected($options[$id], $value, false).'>'.$label.'</option>';
        }

        echo '</select>';

        if (!empty($desc)) {
            echo '<br><span class="description">' . $desc . '</span>';
        }

        break;

    case 'radio':
        $i = 0;
        foreach ($choices as $value => $label) {
            echo '<input class="radio'.$class.'" type="radio" name="wpsellsy_options['.$id.']" id="'.$id.$i.'" value="'.esc_attr($value).'" '.checked($options[$id], $value, false).'> <label for="'.$id.$i.'">'.$label.'</label>';
            if ( $i < count( $options) - 1)
                echo '<br />';
            $i++;
        }

        if (!empty($desc)) {
            echo '<span class="description">'.$desc.'</span>';
        }

        break;

    case 'text':
    default:
        echo '<input class="regular-text'.$class.'" type="text" id="'.$id.'" name="wpsellsy_options['.$id.']" placeholder="'.$std.'" value="'.esc_attr($options[$id]).'" />';

        if (!empty($desc)) {
            echo '<br><span class="description">'.$desc.'</span>';
        }

        break;
}