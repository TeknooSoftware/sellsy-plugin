<?php

namespace UniAlteri\Sellsy\Wordpress;
use UniAlteri\Sellsy\Wordpress\Form\Settings;

/**
 * Class OptionsBag
 * @package UniAlteri\Sellsy
 */
class OptionsBag implements \ArrayAccess
{
    /**
     * Name in wordpress of custom setting for this plugin
     */
    const WORDPRESS_SETTINGS_NAME = 'wpsellsy_options';

    /**
     * Name in wordpress for the filter to validate plugin configuration
     */
    const WORDPRESS_VALIDATE_FILTER = 'wpsellsy_validate_configuration';

    /**
     * @var array
     */
    protected $options = null;

    /**
     * To registers hooks to validate
     */
    public function registerHooks()
    {
        //Register this bag to sanitize data
        if (function_exists('\register_setting')) {
            //This function is only available in admin, so check if this method is available to avoid errors
            \register_setting(self::WORDPRESS_SETTINGS_NAME, self::WORDPRESS_SETTINGS_NAME, array($this, 'sanitize'));
        }

        //Register this bag to filter data
        \add_filter(self::WORDPRESS_VALIDATE_FILTER, array($this, 'validate'), 10, 1);
    }

    /**
     * Reload options from wordpress database and erase/lost change
     * @return $this
     */
    public function reload()
    {
        //Retrieve options from wordpress
        $options = \get_option(self::WORDPRESS_SETTINGS_NAME, null);

        if (empty($options)) {
            $this->options = array();
        } else {
            $this->options = $options;
        }

        return $this;
    }

    /**
     * Save options into wordpress database
     * @return $this
     */
    public function save()
    {
        \update_option(self::WORDPRESS_SETTINGS_NAME, $this->options);

        return $this;
    }

    /**
     * Return options of this plugins under array
     * @return array
     */
    public function toArray()
    {
        return $this->options;
    }

    /**
     * To check if some option are defined for this plugin
     * @return boolean
     */
    public function isDefined()
    {
        return !empty($this->options);
    }

    /**
     * To check if an option exist
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param string $offset name of the option
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        if (empty($this->options)) {
            $this->reload();
        }

        if (!is_string($offset)) {
            return false;
        }

        return isset($this->options[$offset]);
    }

    /**
     * To return an option
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param string $offset name of the option
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        if (empty($this->options)) {
            $this->reload();
        }

        if (!is_string($offset)) {
            return false;
        }

        if (isset($this->options[$offset])) {
            return $this->options[$offset];
        }

        return null;
    }

    /**
     * To define an option
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param string $offset name of the option
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (empty($this->options)) {
            $this->reload();
        }

        if (!is_string($offset)) {
            return;
        }

        $this->options[$offset] = $value;
    }

    /**
     * To unset an option
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param string $offset name of the option
     * @return void
     */
    public function offsetUnset($offset)
    {
        if (empty($this->options)) {
            $this->reload();
        }

        if (!is_string($offset)) {
            return;
        }

        if (isset($this->options[$offset])) {
            unset($this->options[$offset]);
        }
    }

    /**
     * Callback called to validate input fields with business logic (to avoid bad plugin configuration)
     * @param array $input
     * @return mixed
     */
    public function validate($input)
    {
        foreach ($input AS $key => &$val) {
            switch ($key){
                case Settings::CONSUMER_TOKEN:
                    if (strlen($val) != 40) {
                        \add_settings_error(
                            self::WORDPRESS_SETTINGS_NAME,
                            Settings::CONSUMER_TOKEN,
                            __('Le Consumer Token est manquant ou incorrect, vérifiez votre saisie.', 'wpsellsy'),
                            'error'
                        );

                    } else {
                        $val = \sanitize_text_field($val);
                    }
                    break;

                case Settings::CONSUMER_SECRET:
                    if (strlen($val) != 40) {
                        \add_settings_error(
                            self::WORDPRESS_SETTINGS_NAME,
                            Settings::CONSUMER_SECRET,
                            __('Le Consumer Secret est manquant ou incorrect, vérifiez votre saisie.', 'wpsellsy'),
                            'error'
                        );

                    } else {
                        $val = \sanitize_text_field($val);
                    }
                    break;

                case Settings::ACCESS_TOKEN:
                    if (strlen($val) != 40) {
                        \add_settings_error(
                            self::WORDPRESS_SETTINGS_NAME,
                            Settings::ACCESS_TOKEN,
                            __('L\'Utilisateur Token est manquant ou incorrect, vérifiez votre saisie.', 'wpsellsy'),
                            'error'
                        );

                    } else {
                        $val = \sanitize_text_field($val);
                    }
                    break;

                case Settings::ACCESS_SECRET:
                    if (strlen($val) != 40) {
                        \add_settings_error(
                            self::WORDPRESS_SETTINGS_NAME,
                            Settings::ACCESS_SECRET,
                            __('L\'Utilisateur Secret est manquant ou incorrect, vérifiez votre saisie.', 'wpsellsy'),
                            'error'
                        );

                    } else {
                        $val = \sanitize_text_field($val);
                    }
                    break;

                case Settings::SUBMIT_NOTIFICATION:
                    if (empty($val) || !\is_email($val)) {
                        \add_settings_error(
                            self::WORDPRESS_SETTINGS_NAME,
                            Settings::SUBMIT_NOTIFICATION,
                            __('L\'adresse email est manquante ou incorrecte, vérifiez votre saisie ou vous ne recevrez pas d\'email à chaque soumission du formulaire. Les prospects et/ou opportunités seront quand-même créé(e)s.', 'wpsellsy'),
                            'error'
                        );

                    } else {
                        $val = \sanitize_text_field($val);
                    }
                    break;

                case Settings::OPPORTUNITY_CREATION:
                    if (('prospectOpportunity' == $val) && empty($input[Settings::OPPORTUNITY_SOURCE])) {
                        \add_settings_error(
                            self::WORDPRESS_SETTINGS_NAME,
                            Settings::OPPORTUNITY_SOURCE,
                            __('Vous avez choisi de créer une opportunité en plus d\'un prospect, vous devez saisir une source d\'opportunités ou le plugin créera que des prospects.', 'wpsellsy'),
                            'error'
                        );
                    }
                    $val = \sanitize_text_field($val);
                    break;

                case Settings::DISPLAY_FORM_NAME:
                    if (empty($input[Settings::FORM_NAME])) {
                        \add_settings_error(
                            self::WORDPRESS_SETTINGS_NAME,
                            Settings::FORM_NAME,
                            __('Vous avez choisi d\'afficher le nom du formulaire mais vous ne l\'avez pas renseigné.', 'wpsellsy'),
                            'error'
                        );
                    }
                    break;
            }

        }

        return $input;
    }

    /**
     * Callback called to sanitize options
     * @param mixed $input
     * @return mixed
     */
    public function sanitize($input)
    {
        foreach($input AS $key => &$value) {
            if (is_array($value)) {
                foreach ($value as &$sv) {
                    $sv = strip_tags(stripslashes($sv));
                }
            } elseif (Settings::MESSAGE_SENT != $key && Settings::MESSAGE_ERROR != $key) {
                $value = strip_tags(stripslashes($value));
            }
        }

        return \apply_filters(self::WORDPRESS_VALIDATE_FILTER, $input);
    }
}