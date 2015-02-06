<?php

namespace UniAlteri\Sellsy\Form;

class Front
{

    public function addJS()
    {
        if (!is_admin()) {
            if ('choice1' == $this->options['WPIloadjQuery']) {
                wp_deregister_script('jquery');
                wp_register_script(
                    'jquery',
                    SELLSY_WP_JQUERY_URL,
                    false,
                    SELLSY_WP_JQUERY_VERSION
                );
                wp_enqueue_script('jquery');
            }

            if ('choice1' == $this->options['WPIjsValid']) {
                wp_register_script(
                    'wpsellsyjsvalid',
                    plugins_url('/js/jquery.validate.min.js', SELLSY_WP_PATH_FILE),
                    ['jquery'],
                    '1.0',
                    true
                );

                wp_enqueue_script('wpsellsyjsvalid');
            }
        }
    }

    public function addCSS()
    {
        if (!is_admin()) {
            wp_register_style(
                'wpsellsystyles',
                plugins_url('/css/wp_sellsy.css', SELLSY_WP_PATH_FILE),
                [],
                '1.0',
                'screen'
            );

            wp_enqueue_style('wpsellsystyles');
        }
    }

    function shortcode() {

        add_shortcode('wpsellsy', array($this, 'wpi_shortcode_body'));

    }

    function shortcodeBody($attr, $content = null) {

        include_once WPI_PATH_INC . '/wp_sellsy-pub-page.php';

    }

    function widget() {

        include_once WPI_PATH_INC . '/wp_sellsy-widget.class.php';

    }

    function formValidate() {

        $options = get_option('wpsellsy_options');
        $txtVal = array(
            'WPIraisonsociale' => __('de la raison sociale', 'wpsellsy'),
            'WPIsiteweb' => __('du site internet', 'wpsellsy'),
            'WPInomcont' => __('du nom du contact', 'wpsellsy'),
            'WPIprenomcont' => __('du prénom du contact', 'wpsellsy'),
            'WPIfonccont' => __('de la fonction du contact', 'wpsellsy'),
            'WPItel' => __('du téléphone', 'wpsellsy'),
            'WPIport' => __('du portable', 'wpsellsy'),
            'WPIemail' => __('de l\'email', 'wpsellsy'),
            'WPIfax' => __('du fax', 'wpsellsy'),
            'WPInote' => __('de la note', 'wpsellsy')
        );
        if (isset($options['WPIjsValid']) AND $options['WPIjsValid'] == 'choice1') {
            ?>
            <script type="text/javascript">
                //<![CDATA[
                jQuery(document).ready(function($) {
                    $('#wp-sellsy-form').validate({
                        rules: {
                            <?php
                                foreach($options AS $key => $value) {

                                    if ($value == 'choice3') {
                                        switch ($key) {
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

                                foreach($options AS $key => $value) {

                                    if ($value == 'choice3') {
                                        echo $key . ': "' . __ ('Merci de vérifier la saisie ', 'wpsellsy') . $txtVal[$key] . '", ';
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

    function pointersStyles($hook_suffix) {

        $wp_sellsyScriptStyles = false;
        $dismissed_pointers = explode(',', get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));

        if(! in_array('wpi_pointer', $dismissed_pointers)) {
            $wp_sellsyScriptStyles = true;
            add_action('admin_print_footer_scripts', array($this, 'wpi_pointers_scripts'));
        }

        if($wp_sellsyScriptStyles) {
            wp_enqueue_style('wp-pointer');
            wp_enqueue_script('wp-pointer');
        }

    }

    function pointersScripts() {

        $pointer_content  = '<h3>WP Sellsy Prospection</h3>';
        $pointer_content .= '<p><img src="../wp-content/plugins/wp_sellsy/img/sellsy_34.png" alt="" style="float:left;margin-right:10px" /> ' . __('Vous avez installé le plugin WP Sellsy Prospection. Cliquez ici pour procéder à sa configuration', 'wpsellsy') . '</p>';
        ?>
        <script type="text/javascript">
            //<![CDATA[
            jQuery(document).ready(function($) {
                $('#toplevel_page_wpi-admPage').pointer({
                    content:	'<?php echo $pointer_content; ?>',
                    position: 	{
                        edge:	'left',
                        align:	'right'
                    },
                    pointerWidth:	350,
                    close:			function() {
                        $.post(ajaxurl, {
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