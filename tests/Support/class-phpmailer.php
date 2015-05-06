<?php

class PHPMailer
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
     * To reset
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