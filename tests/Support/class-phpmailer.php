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
class phpmailer
{
    /**
     * @var string
     */
    public $Subject;

    /**
     * @var string
     */
    public static $From;

    /**
     * @var string[]
     */
    public static $Addresses = array();

    /**
     * @var string
     */
    public static $Msg;

    /**
     * @var bool
     */
    public static $Result = true;

    /**
     * To reset.
     */
    public function __construct()
    {
        self::$From = '';
        self::$Msg = '';
        self::$Addresses = array();
    }

    /**
     * @param string $from
     */
    public function SetFrom($from)
    {
        self::$From = $from;
    }

    /**
     * @param string $address
     */
    public function AddAddress($address)
    {
        self::$Addresses[] = $address;
    }

    /**
     * @param string $msg
     */
    public function MsgHTML($msg)
    {
        self::$Msg = $msg;
    }

    /***
     * @return bool
     */
    public function Send()
    {
        return self::$Result;
    }
}
