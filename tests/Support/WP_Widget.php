<?php

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
     * PHP5 constructor.     *
     * @param string $id_base         Optional Base ID for the widget, lowercase and unique. If left empty,
     *                                a portion of the widget's class name will be used Has to be unique.
     * @param string $name            Name for the widget displayed on the configuration page.
     * @param array  $widget_options  Optional. Widget options. See {@see wp_register_sidebar_widget()} for
     *                                information on accepted arguments. Default empty array.
     * @param array  $control_options Optional. Widget control options. See {@see wp_register_widget_control()}
     *                                for information on accepted arguments. Default empty array.
     */
    public function __construct($id_base='sellsy_widget', $name='', $widget_options = array(), $control_options = array())
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
}