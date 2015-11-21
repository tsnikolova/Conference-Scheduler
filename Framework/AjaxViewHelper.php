<?php
namespace Framework;
class AjaxViewHelper
{
    private static $_instance = null;
    private $_ajax;
    private function  __construct()
    {
    }
    public static function init()
    {
        if (self::$_instance == null) {
            self::$_instance = new AjaxViewHelper();
        }
        return self::$_instance;
    }
    public function initForm($action, $method = 'post', array $data = array(), $samePageToken = false)
    {
        if (strtolower($method) !== 'post' && strtolower($method) !== 'get') {
            $ajax = "$.ajax({method: \"POST\", url: \"$action\",";
            $ajax .= "data: {";
            $ajax .= "_method: \"$method\",";
        } else {
            $method = strtoupper($method);
            $ajax = "$.ajax({method: \"$method\", url: \"$action\",";
            $ajax .= "data: {";
        }
        foreach ($data as $k => $v) {
            $ajax .= "$k: \"$v\",";
        }
        if (strtolower($method) !== 'get') {
            $token = Token::getToken($samePageToken);
            $ajax .= "_token: \"$token\"";
        }
        $ajax .= "}})";
        $this->_ajax = $ajax;
        return $this;
    }
    public function initCallback($body)
    {
        $this->_ajax .= ".done(
            $body
        );";
        return $this;
    }
    public function render()
    {
        // $this->_ajax = "<script>" . $this->_ajax . '</script>';
        echo $this->_ajax;
        $this->_ajax = "";
    }
}