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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
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

/**
 * Class Widget
 * Wordpress Widget to display in the public front an HTML form to generate leads in Sellsy.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://agence.net.ua)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
class Widget extends \WP_Widget
{
    /**
     * PHP5 constructor.     *.
     *
     * @param string $id_base         Optional Base ID for the widget, lowercase and unique. If left empty,
     *                                a portion of the widget's class name will be used Has to be unique.
     * @param string $name            Name for the widget displayed on the configuration page.
     * @param array  $widget_options  Optional. Widget options. See {@see wp_register_sidebar_widget()} for
     *                                information on accepted arguments. Default empty array.
     * @param array  $control_options Optional. Widget control options. See {@see wp_register_widget_control()}
     *                                for information on accepted arguments. Default empty array.
     */
    public function __construct($id_base = 'sellsy_widget', $name = '', $widget_options = array(), $control_options = array())
    {
        if (empty($name)) {
            $name = __('WP Sellsy', 'wpsellsy');
        }

        if (empty($widget_options)) {
            $widget_options = array(
                'classname' => 'sellsy_widget_single',
                'description' => __('Affiche le widget WP Sellsy', 'wpsellsy'),
            );
        }

        parent::__construct($id_base, $name, $widget_options, $control_options);
    }

    /**
     * Echo the widget content.
     *
     * Subclasses should over-ride this function to generate their widget code.
     *
     * @param array $args     Display arguments including before_title, after_title,
     *                        before_widget, and after_widget.
     * @param array $instance The settings for the particular instance of the widget.
     */
    public function widget($args, $instance)
    {
        //Extract title
        $title = '';
        if (isset($instance['title'])) {
            $title = \apply_filters('widget_title', $instance['title']);
        }

        //Extract body
        $out = '';
        if (isset($instance['text'])) {
            $out = $instance['text'];
        }

        //Extract options
        $beforeWidget = '';
        if (isset($args['before_widget'])) {
            $beforeWidget = $args['before_widget'];
        }

        $beforeTitle = '';
        if (isset($args['before_title'])) {
            $beforeTitle = $args['before_title'];
        }

        $afterTitle = '';
        if (isset($args['after_title'])) {
            $afterTitle = $args['after_title'];
        }

        $afterWidget = '';
        if (isset($args['after_widget'])) {
            $afterWidget = $args['after_widget'];
        }

        if (!empty($out) && is_readable(SELLSY_WP_PATH_INC.'/widget.php')) {
            include SELLSY_WP_PATH_INC.'/widget.php';
        }
    }

    /**
     * Update a particular instance.
     *
     * This function should check that $new_instance is set correctly. The newly-calculated
     * value of `$instance` should be returned. If false is returned, the instance won't be
     * saved/updated.
     *
     * @param array $newInstance New settings for this instance as input by the user via
     *                           {@see WP_Widget::form()}.
     * @param array $oldInstance Old settings for this instance.
     *
     * @return array Settings to save or bool false to cancel saving.
     */
    public function update($newInstance, $oldInstance)
    {
        if (isset($oldInstance['title'])) {
            $newInstance['title'] = strip_tags($oldInstance['title']);
        }

        if (isset($oldInstance['text'])) {
            $newInstance['text'] = strip_tags($oldInstance['text']);
        }

        return $newInstance;
    }

    /**
     * Output the settings update form.
     *
     * @param array $instance Current settings.
     *
     * @return string Default return is 'noform'.
     */
    public function form($instance)
    {
        $title = '';
        if (isset($instance['title'])) {
            $title = \esc_attr($instance['title']);
        }

        $text = '';
        if (isset($instance['text'])) {
            $text = \esc_attr($instance['text']);
        }

        if (is_readable(SELLSY_WP_PATH_INC.'/form.php')) {
            include SELLSY_WP_PATH_INC.'/form.php';
        }
    }
}
