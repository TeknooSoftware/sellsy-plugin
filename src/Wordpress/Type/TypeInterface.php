<?php

namespace UniAlteri\Sellsy\Wordpress\Type;

use UniAlteri\Sellsy\Wordpress\Form\CustomField;

interface TypeInterface
{
    /**
     * @return CustomField[]
     */
    public function getStandardFields();

    /**
     * @param string $fieldName
     * @param mixed $value
     * @param array $finalSource
     */
    public function populateParams($fieldName, &$value, &$finalSource);

    /**
     * @param string $fieldName
     * @param mixed $value
     * @return boolean
     * @throws \Exception
     */
    public function validateField($fieldName, &$value);
}