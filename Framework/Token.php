<?php
namespace Framework;
/**
 * Class Token - CSRF Token
 * @package Framework
 */
class Token
{
    private static $_instance;
    private function __construct()
    {
    }
    public static function init()
    {
        if (self::$_instance == null) {
            self::$_instance = new Token();
        }
        return self::$_instance;
    }
    public static function render($samePage = false)
    {
        if (!$samePage) {
            self::generateToken();
        }
        $html = '<input type="hidden" name="_token" value="' . App::getInstance()->getSession()->_token . '">';
        echo $html;
    }
    public static function validates($token)
    {
        $isValid = App::getInstance()->getSession()->_token === $token;
        self::generateToken();
        return $isValid;
    }
    public static function getToken($samePageToken = false)
    {
        if (!$samePageToken) {
            self::generateToken();
        }
        return App::getInstance()->getSession()->_token;
    }
    private static function generateToken()
    {
        App::getInstance()->getSession()->_token = base64_encode(openssl_random_pseudo_bytes(64));
    }
}