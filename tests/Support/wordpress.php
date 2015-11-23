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
 *
 * @copyright   Copyright (c) 2009-2016 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 *
 * @version     0.8.0
 */
global $methodCalled;
global $methodArgs;
$methodCalled = array();
$methodArgs = array();

/**
 * To allow tests to define some stub method to tests plugin's components.
 *
 * @param string $methodName
 * @param mixed  $methodArgs
 * @param mixed  $willReturn
 */
function prepareMock($methodName, $methodArgs, $willReturn)
{
    global $methodMocks;
    if (is_array($methodArgs)) {
        $argsHash = md5(serialize($methodArgs));
        $methodMocks[$methodName][$argsHash] = $willReturn;
    } else {
        $methodMocks[$methodName][$methodArgs] = $willReturn;
    }
}

/**
 * To register a call to a wordpress's api method and a return a stub if there defined.
 *
 * @param string $methodName
 * @param array  $methodArgsValues
 *
 * @return null|mixed
 */
function mockMethodTrace($methodName, $methodArgsValues)
{
    global $methodCalled;
    global $methodArgs;
    global $methodMocks;
    $methodCalled[] = $methodName;
    $methodArgs[] = $methodArgsValues;

    $serializable = true;
    foreach ($methodArgsValues as $arg) {
        if ($arg instanceof \Closure) {
            $serializable = false;
        }
    }

    if (true === $serializable) {
        $argsHash = md5(serialize($methodArgsValues));
    } else {
        $argsHash = '*';
    }

    if (isset($methodMocks[$methodName][$argsHash])) {
        if (is_callable($methodMocks[$methodName][$argsHash])) {
            return call_user_func_array($methodMocks[$methodName][$argsHash], $methodArgsValues);
        } else {
            return $methodMocks[$methodName][$argsHash];
        }
    }

    if (isset($methodMocks[$methodName]['*'])) {
        if (is_callable($methodMocks[$methodName]['*'])) {
            return call_user_func_array($methodMocks[$methodName]['*'], $methodArgsValues);
        } else {
            return $methodMocks[$methodName]['*'];
        }
    }

    return;
}

/**
 * Hooks a function on to a specific action.
 *
 * @param string   $tag
 * @param callback $function_to_add
 * @param int      $priority
 * @param int      $accepted_args
 *
 * @return bool Will always return true.
 */
function add_action($tag, $function_to_add, $priority = 10, $accepted_args = 1)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Set the deactivation hook for a plugin.
 *
 * @param string   $file     The filename of the plugin including the path.
 * @param callback $function The function hooked to the 'deactivate_PLUGIN' action.
 *
 * @return mixed
 */
function register_deactivation_hook($file, $function)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Add hook for shortcode tag.
 *
 * @uses $shortcode_tags *
 *
 * @param string   $tag  Shortcode tag to be searched in post content.
 * @param callable $func Hook to run when shortcode is found.
 *
 * @return mixed
 */
function add_shortcode($tag, $func)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Register a widget.
 *
 * @param string $widget_class The name of a class that extends WP_Widget
 *
 * @return mixed
 */
function register_widget($widget_class)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Kill WordPress execution and display HTML message with error message. *.
 *
 * @param string           $message
 * @param string|int       $title
 * @param string|array|int $args
 *
 * @return mixed
 */
function wp_die($message = '', $title = '', $args = array())
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Call the functions added to a filter hook.
 *
 * @param string $tag   The name of the filter hook.
 * @param mixed  $value The value on which the filters hooked to `$tag` are applied on.
 *
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_filters($tag, $value)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Escaping for HTML attributes.
 *
 * @param string $text
 *
 * @return string
 */
function esc_attr($text)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Load a plugin's translated strings.
 *
 * @param string      $domain          Unique identifier for retrieving translated strings
 * @param string|bool $deprecated      Use the $plugin_rel_path parameter instead.
 * @param string|bool $plugin_rel_path Optional. Relative path to WP_PLUGIN_DIR where the .mo file resides.
 *
 * @return bool True when textdomain is successfully loaded, false otherwise.
 */
function load_plugin_textdomain($domain, $deprecated = false, $plugin_rel_path = false)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Removes option by name. Prevents removal of protected WordPress options.
 *
 * @param string $option Name of option to remove. Expected to not be SQL-escaped.
 *
 * @return bool True, if option is successfully deleted. False on failure.
 */
function delete_option($option)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Register a settings error to be displayed to the user *.
 *
 * @param string $setting Slug title of the setting to which this error applies
 * @param string $code    Slug-name to identify the error. Used as part of 'id' attribute in HTML output.
 * @param string $message The formatted message text to display to the user (will be shown inside styled
 * @param string $type    The type of message it is, controls HTML class. Use 'error' or 'updated'.
 *
 * @return mixed
 */
function add_settings_error($setting, $code, $message, $type = 'error')
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Verify that correct nonce was used with time limit.
 *
 * @param string     $nonce  Nonce that was used in the form to verify
 * @param string|int $action Should give context to what is taking place and be the same when nonce was created.
 *
 * @return bool Whether the nonce check passed or failed.
 */
function wp_verify_nonce($nonce, $action = -1)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Register a setting and its sanitization callback.
 *
 * @param string          $option_group      A settings group name. Should correspond to a whitelisted option key name.
 * @param string          $option_name       The name of an option to sanitize and save.
 * @param callable|string $sanitize_callback A callback function that sanitizes the option's value.
 *
 * @return mixed
 */
function register_setting($option_group, $option_name, $sanitize_callback = '')
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Hook a function or method to a specific filter action.
 *
 * @param string   $tag             The name of the filter to hook the $function_to_add callback to.
 * @param callback $function_to_add The callback to be run when the filter is applied.
 * @param int      $priority        Optional. Used to specify the order in which the functions
 * @param int      $accepted_args   Optional. The number of arguments the function accepts. Default 1.
 *
 * @return bool true
 */
function add_filter($tag, $function_to_add, $priority = 10, $accepted_args = 1)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Retrieve option value based on name of option.
 *
 * @param string $option  Name of option to retrieve. Expected to not be SQL-escaped.
 * @param mixed  $default Optional. Default value to return if the option does not exist.
 *
 * @return mixed Value set for the option.
 */
function get_option($option, $default = false)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Update the value of an option that was already added.
 *
 * @param string $option Option name. Expected to not be SQL-escaped.
 * @param mixed  $value  Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
 *
 * @return bool False if value was not updated and true if value was updated.
 */
function update_option($option, $value)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Emulate the WP native sanitize_text_field function in a %%variable%% safe way.
 *
 * @param string $value *
 *
 * @return string
 */
function sanitize_text_field($value)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Add a new section to a settings page.
 *
 * @param string $id       Slug-name to identify the section. Used in the 'id' attribute of tags.
 * @param string $title    Formatted title of the section. Shown as the heading for the section.
 * @param string $callback Function that echos out any content at the top of the section (between heading and fields).
 * @param string $page     The slug-name of the settings page on which to show the section. Built-in pages include 'general', 'reading', 'writing', 'discussion', 'media', etc. Create your own using add_options_page();
 *
 * @return mixed
 */
function add_settings_section($id, $title, $callback, $page)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Retrieve the translation of $text. If there is no translation,
 * or the text domain isn't loaded, the original text is returned.
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *
 * @return string Translated text.
 */
function __($text, $domain = 'default')
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Merge user defined arguments into defaults array.
 *
 * @param string|array $args     Value to merge with $defaults
 * @param array|string $defaults Optional. Array that serves as the defaults. Default empty.
 *
 * @return array Merged user defined values with defaults.
 */
function wp_parse_args($args, $defaults = '')
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Add a new field to a section of a settings page *.
 *
 * @param string $id       Slug-name to identify the field. Used in the 'id' attribute of tags.
 * @param string $title    Formatted title of the field. Shown as the label for the field during output.
 * @param string $callback Function that fills the field with the desired form inputs. The function should echo its output.
 * @param string $page     The slug-name of the settings page on which to show the section (general, reading, writing, ...).
 * @param string $section  The slug-name of the section of the settings page in which to show the box (default, ...).
 * @param array  $args     Additional arguments
 *
 * @return mixed
 */
function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = array())
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Enqueue a script. *
 * Registers the script if $src provided (does NOT overwrite), and enqueues it.
 *
 * @param string      $handle    Name of the script.
 * @param string|bool $src       Path to the script from the root directory of WordPress. Example: '/js/myscript.js'.
 * @param array       $deps      An array of registered handles this script depends on. Default empty array.
 * @param string|bool $ver       Optional. String specifying the script version number, if it has one.
 * @param bool        $in_footer Optional. Whether to enqueue the script before </head> or before </body>.
 *
 * @return mixed
 */
function wp_enqueue_script($handle, $src = false, $deps = array(), $ver = false, $in_footer = false)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Retrieve a URL within the plugins or mu-plugins directory.
 * Defaults to the plugins directory URL if no arguments are supplied.
 *
 * @param string $path   Optional. Extra path appended to the end of the URL, including
 *                       the relative directory if $plugin is supplied. Default empty.
 * @param string $plugin Optional. A full path to a file inside a plugin or mu-plugin.
 *                       The URL will be relative to its directory. Default empty.
 *                       Typically this is done by passing `__FILE__` as the argument.
 *
 * @return string Plugins URL link with optional paths appended.
 */
function plugins_url($path = '', $plugin = '')
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Localize a script.
 *
 * @param string $handle      Script handle the data will be attached to.
 * @param string $object_name Name for the JavaScript object. Passed directly, so it should be qualified JS variable.
 *                            Example: '/[a-zA-Z0-9_]+/'.
 * @param array  $l10n        The data itself. The data can be either a single or multi-dimensional array.
 *
 * @return bool True if the script was successfully localized, false otherwise.
 */
function wp_localize_script($handle, $object_name, $l10n)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Retrieve the url to the admin area for the current site.
 *
 * @param string $path   Optional path relative to the admin url.
 * @param string $scheme The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http' or 'https' can be passed to force those schemes.
 *
 * @return string Admin url link with optional path appended.
 */
function admin_url($path = '', $scheme = 'admin')
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Creates a cryptographic token tied to a specific action, user, and window of time. *.
 *
 * @param string|int $action Scalar value to add context to the nonce.
 *
 * @return string The token.
 */
function wp_create_nonce($action = -1)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Verifies that an email is valid.
 *
 * @param string $email      Email address to verify.
 * @param bool   $deprecated Deprecated.
 *
 * @return string|bool Either false or the valid email address.
 */
function is_email($email, $deprecated = false)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Whether the current request is for an administrative interface page.
 *
 * @return bool True if inside WordPress administration interface, false otherwise.
 */
function is_admin()
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Register a CSS stylesheet. *.
 *
 * @param string      $handle Name of the stylesheet.
 * @param string|bool $src    Path to the stylesheet from the WordPress root directory. Example: '/css/mystyle.css'.
 * @param array       $deps   An array of registered style handles this stylesheet depends on. Default empty array.
 * @param string|bool $ver    String specifying the stylesheet version number.
 * @param string      $media  Optional. The media for which this stylesheet has been defined.
 *
 * @return mixed
 */
function wp_register_style($handle, $src, $deps = array(), $ver = false, $media = 'all')
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Enqueue a CSS stylesheet. *.
 *
 * @param string      $handle Name of the stylesheet.
 * @param string|bool $src    Path to the stylesheet from the root directory of WordPress. Example: '/css/mystyle.css'.
 * @param array       $deps   An array of registered style handles this stylesheet depends on. Default empty array.
 * @param string|bool $ver    String specifying the stylesheet version number, if it has one.
 * @param string      $media  Optional. The media for which this stylesheet has been defined.
 *
 * @return mixed
 */
function wp_enqueue_style($handle, $src = false, $deps = array(), $ver = false, $media = 'all')
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Add a top level menu page.
 *
 * @param string          $page_title The text to be displayed in the title tags of the page when the menu is selected
 * @param string          $menu_title The text to be used for the menu
 * @param string          $capability The capability required for this menu to be displayed to the user.
 * @param string          $menu_slug  The slug name to refer to this menu by (should be unique for this menu)
 * @param callback|string $function   The function to be called to output the content for this page.
 * @param string          $icon_url   The url to the icon to be used for this menu.
 * @param int             $position   The position in the menu order this one should appear
 *
 * @return string The resulting page's hook_suffix
 */
function add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function = '', $icon_url = '', $position = null)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Whether current user has capability or role. *.
 *
 * @param string $capability Capability or role name.
 *
 * @return bool
 */
function current_user_can($capability)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Display translated text.
 *
 * @param string $text   Text to translate.
 * @param string $domain Optional. Text domain. Unique identifier for retrieving translated strings.
 *
 * @return mixed
 */
function _e($text, $domain = 'default')
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Display settings errors registered by {@see add_settings_error()}.
 *
 * @param string $setting        Optional slug title of a specific setting who's errors you want.
 * @param bool   $sanitize       Whether to re-sanitize the setting value before returning errors.
 * @param bool   $hide_on_update If set to true errors will not be shown if the settings page has already been submitted.
 *
 * @return mixed
 */
function settings_errors($setting = '', $sanitize = false, $hide_on_update = false)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Output nonce, action, and option_page fields for a settings page.
 *
 * @param string $option_group A settings group name. This should match the group name used in register_setting().
 *
 * @return mixed
 */
function settings_fields($option_group)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Prints out all settings sections added to a particular settings page.
 *
 * @param string $page The slug name of the page whos settings sections you want to output
 *
 * @return mixed
 */
function do_settings_sections($page)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * Echoes a submit button, with provided text and appropriate class(es).
 *
 * @since 3.1.0
 * @see get_submit_button()
 *
 * @param string       $text             The text of the button (defaults to 'Save Changes')
 * @param string       $type             Optional. The type and CSS class(es) of the button. Core values
 *                                       include 'primary', 'secondary', 'delete'. Default 'primary'
 * @param string       $name             The HTML name of the submit button. Defaults to "submit". If no
 *                                       id attribute is given in $other_attributes below, $name will be
 *                                       used as the button's id.
 * @param bool         $wrap             True if the output button should be wrapped in a paragraph tag,
 *                                       false otherwise. Defaults to true
 * @param array|string $other_attributes Other attributes that should be output with the button, mapping
 *                                       attributes to their values, such as setting tabindex to 1, etc.
 *                                       These key/value attribute pairs will be output as attribute="value",
 *                                       where attribute is the key. Other attributes can also be provided
 *                                       as a string such as 'tabindex="1"', though the array format is
 *                                       preferred. Default null.
 *
 * @return mixed
 */
function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}

/**
 * @param string $path
 *
 * @return string
 */
function plugin_basename($path)
{
    return dirname($path);
}

/**
 * @param string $shortcode
 *
 * @return string
 */
function do_shortcode($shortcode)
{
    return mockMethodTrace(__FUNCTION__, func_get_args());
}
