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
class WP_Widget
{
    /**
     * @var string
     */
    protected $idBase;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $widgetOptions;

    /**
     * @var string
     */
    protected $controlOptions = null;

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
        $this->idBase = $id_base;
        $this->name = $name;
        $this->widgetOptions = $widget_options;
        $this->controlOptions = $control_options;
    }

    /**
     * @return string
     */
    public function getIdBase()
    {
        return $this->idBase;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array|string
     */
    public function getWidgetOptions()
    {
        return $this->widgetOptions;
    }

    /**
     * @return array|string
     */
    public function getControlOptions()
    {
        return $this->controlOptions;
    }

    /**
     * Constructs id attributes for use in {@see WP_Widget::form()} fields.
     *
     * This function should be used in form() methods to create id attributes
     * for fields to be saved by {@see WP_Widget::update()}.
     *
     * @since 2.8.0
     *
     * @param string $field_name Field name.
     *
     * @return string ID attribute for `$field_name`.
     */
    public function get_field_id($field_name)
    {
        return $field_name;
    }

    /**
     * Constructs name attributes for use in form() fields.
     *
     * This function should be used in form() methods to create name attributes for fields to be saved by update()
     *
     * @param string $field_name Field name
     *
     * @return string Name attribute for $field_name
     */
    public function get_field_name($field_name)
    {
        return $field_name;
    }
}
