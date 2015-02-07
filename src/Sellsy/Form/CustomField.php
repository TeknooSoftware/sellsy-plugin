<?php

namespace UniAlteri\Sellsy\Wordpress\Form;

class CustomField
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $defaultValue;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param int $id
     * @param string $type
     * @param string $name
     * @param string $code
     * @param string $description
     * @param string $defaultValue
     * @param array $options
     */
    public function __construct($id, $type, $name, $code, $description, $defaultValue, $options)
    {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
        $this->defaultValue = $defaultValue;

        $optionsVals = [];
        foreach ($options as $option) {
            $optionsVals[$option->rank] = [
                'id' => $option->id,
                'value' => $option->value
            ];
        }

        $this->options = $optionsVals;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}