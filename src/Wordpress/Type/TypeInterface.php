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
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 *
 * @version     0.8.0
 */

namespace UniAlteri\Sellsy\Wordpress\Type;

use UniAlteri\Sellsy\Wordpress\Form\Field;

/**
 * Interface TypeInterface.
 *
 * @copyright   Copyright (c) 2009-2015 Uni Alteri (http://uni-alteri.com)
 *
 * @link        http://teknoo.it/sellsy-plugin Project website
 *
 * @license     http://teknoo.it/sellsy-plugin/license/mit         MIT License
 * @license     http://teknoo.it/sellsy-plugin/license/gpl-3.0     GPL v3 License
 * @author      Richard Déloge <r.deloge@uni-alteri.com>
 */
interface TypeInterface
{
    /**
     * Get the list of standards fields defined for this type by Sellsy.
     *
     * @return Field[]
     */
    public function getStandardFields();

    /**
     * To populate this type from an array of values with the correspondence
     * table defined for this type.
     *
     * @param string $fieldName
     * @param mixed  $value
     * @param array  $finalSource
     */
    public function populateParams($fieldName, &$value, &$finalSource);

    /**
     * To check the validity of a value for a destination field.
     *
     * @param string $fieldName
     * @param mixed  $value
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function validateField($fieldName, &$value);
}
