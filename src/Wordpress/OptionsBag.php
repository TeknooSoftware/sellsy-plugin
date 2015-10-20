<?php

/**
 * Sellsy Wordpress plugin.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@uni-alteri.com so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.0
 */

namespace UniAlteri\Sellsy\Wordpress;

use UniAlteri\Sellsy\Wordpress\Form\Settings;

/**
 * Class OptionsBag.
 *
 * @copyright   Copyright (c) 2009-2016 Uni Alteri (http://uni-alteri.com)
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (r.deloge@uni-alteri.com)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class OptionsBag implements \ArrayAccess
{
    /**
     * Name in wordpress of custom setting for this plugin.
     */
    const WORDPRESS_SETTINGS_NAME = 'wpsellsy_options';

    /**
     * Name in wordpress for the filter to validate plugin configuration.
     */
    const WORDPRESS_VALIDATE_FILTER = 'wpsellsy_validate_configuration';

    /**
     * List of settings defined by the administrator for this plugin.
     *
     * @var array
     */
    protected $options = null;

    /**
     * To registers Wordpress hooks to validate settings' values.
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

    /**:
     * Reload settings/options from Wordpress database and erase/lost change.
     *
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
     * Save settings/options into Wordpress database.
     *
     * @return $this
     */
    public function save()
    {
        \update_option(self::WORDPRESS_SETTINGS_NAME, $this->options);

        return $this;
    }

    /**
     * Return all settings/options of this plugins under array from the Wordpress database.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->options;
    }

    /**
     * To check if some settings/options are defined for this plugin in the Wordpress database.
     *
     * @return bool
     */
    public function isDefined()
    {
        return !empty($this->options);
    }

    /**
     * To check if a setting/option exist.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param string $offset name of the option
     *
     * @return bool true on success or false on failure.
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
     * To return a setting/option.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param string $offset name of the option
     *
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

        return;
    }

    /**
     * To define a setting/option.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param string $offset name of the option
     * @param mixed  $value
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
     * To unset a setting/option.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param string $offset name of the option
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
     * Callback called to validate input fields with business logic (to avoid bad plugin configuration).
     *
     * @param array $input
     *
     * @return mixed
     */
    public function validate($input)
    {
        foreach ($input as $key => &$val) {
            switch ($key) {
                case Settings::CONSUMER_TOKEN:
                    if (strlen($val) != 40) {
                        \add_settings_error(
                            self::WORDPRESS_SETTINGS_NAME,
                            Settings::CONSUMER_TOKEN,
                            __('The Consumer Token is missing or invalid, please check it.', 'wpsellsy'),
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
                            __('The Consumer Secret is missing or invalid, please check it.', 'wpsellsy'),
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
                            __('The User Token is missing or invalid, please check it', 'wpsellsy'),
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
                            __('The User Secret is missing or invalid, please check it.', 'wpsellsy'),
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
                            __('Your email adress is missing or invalid, please check it to receive notifications. Prospects will be created anyway.', 'wpsellsy'),
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
                            __('You must select the source used to create opportunities.', 'wpsellsy'),
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
                            __('You must input the form\'s title.', 'wpsellsy'),
                            'error'
                        );
                    }
                    break;
            }
        }

        return $input;
    }

    /**
     * Callback called to sanitize settings/options.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function sanitize($input)
    {
        foreach ($input as $key => &$value) {
            if (is_array($value)) {
                foreach ($value as &$sv) {
                    $sv = strip_tags(stripslashes($sv));
                }
            } elseif (Settings::MESSAGE_SENT != $key && Settings::MESSAGE_ERROR != $key && Settings::FORM_CUSTOM_HEADER != $key && Settings::FORM_CUSTOM_FOOTER != $key) {
                $value = strip_tags(stripslashes($value));
            }
        }

        return \apply_filters(self::WORDPRESS_VALIDATE_FILTER, $input);
    }
}
