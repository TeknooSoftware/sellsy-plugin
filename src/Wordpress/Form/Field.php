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

namespace UniAlteri\Sellsy\Wordpress\Form;

/**
 * Class Field
 * Class to manage mandatories fields defined for each sellsy's type and
 * customs fields defined in the user's sellsy account, fetched via the API
 * to be manipulate in this plugin.
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
class Field
{
    /**
     * Id of this custom field in Sellsy.
     *
     * @var int
     */
    protected $id;

    /**
     * Type of this custom field (see the Sellsy's documentation).
     *
     * @var string
     */
    protected $type;

    /**
     * Name of this custom field in Sellsy.
     *
     * @var string
     */
    protected $name;

    /**
     * Code of this custom field in Sellsy (see the Sellsy's documentation).
     *
     * @var string
     */
    protected $code;

    /**
     * Description defined for this custom field in the Sellsy API.
     *
     * @var string
     */
    protected $description;

    /**
     * Default value defined for this custom field in the Sellsy API.
     *
     * @var string
     */
    protected $defaultValue;

    /**
     * Options defined for this custom field in the Sellsy API.
     *
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $isCustomField = false;

    /**
     * If this custom field is defined as mandatory in the Sellsy account.
     * If it's the case but the field is empty, the default value is used.
     *
     * @var bool
     */
    protected $isRequiredField = false;

    /**
     * Constructor to initialize this custom field and parse the Sellsy API's answer.
     *
     * @param int    $id
     * @param string $type
     * @param string $name
     * @param string $code
     * @param string $description
     * @param string $defaultValue
     * @param array  $options
     * @param bool   $isCustomField
     * @param bool   $isRequiredField
     */
    public function __construct($id, $type, $name, $code, $description, $defaultValue, $options, $isCustomField, $isRequiredField = false)
    {
        $this->id = $id;
        $this->type = $type;
        $this->name = $name;
        $this->code = $code;
        $this->description = $description;
        $this->defaultValue = $defaultValue;
        if ('boolean' == $isRequiredField && empty($defaultValue)) {
            $this->defaultValue = 'N';
        }
        $this->isCustomField = $isCustomField;
        $this->isRequiredField = $isRequiredField;

        $optionsVals = array();
        if (!empty($options)) {
            //Parse options of this field defined in Sellsy
            foreach ($options as $option) {
                if (isset($option->isDefault) && 'Y' == $option->isDefault) {
                    //Extract default value from the list
                    $this->defaultValue = $option->value;
                }

                $optionsVals[$option->rank] = array(
                    'id' => $option->id,
                    'value' => $option->value,
                );
            }
        }

        $this->options = $optionsVals;
    }

    /**
     * Get the Id of this custom field in Sellsy.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the type of this custom field (see the Sellsy's documentation).
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the name of this custom field in Sellsy.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the code of this custom field in Sellsy (see the Sellsy's documentation).
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the description defined for this custom field in the Sellsy API.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the default value defined for this custom field in the Sellsy API.
     *
     * @return string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Get options defined for this custom field in the Sellsy API.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * To know is it's a mandatory option of the sellsy's type or if it's a custom field
     * defined in the Sellsy account for this sellsy's type.
     *
     * @return bool
     */
    public function isCustomField()
    {
        return !empty($this->isCustomField);
    }

    /**
     * To know if this field is mandatory to create a new entity in Sellsy.
     *
     * @return bool
     */
    public function isRequiredField()
    {
        return !empty($this->isRequiredField);
    }

    /**
     * To convert this field to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
