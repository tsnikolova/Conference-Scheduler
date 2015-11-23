<?php

namespace Models\ViewModels\UserController;
class ProfileViewModel
{
    private $username;
    private $isSiteAdmin;
    private $isEditor;
    private $isModerator;
    private $balance;
    function __construct($username, $isSiteAdmin, $balance, $isEditor, $isModerator)
    {
        $this->username = $username;
        $this->isSiteAdmin = $isSiteAdmin;
        $this->isEditor = $isEditor;
        $this->isModerator = $isModerator;
        $this->balance = $balance;
    }
    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }
    /**
     * @return mixed
     */
    public function getIsSiteAdmin()
    {
        return $this->isSiteAdmin;
    }
    public function getIsEditor()
    {
        return $this->isEditor;
    }
    public function getBalance()
    {
        return $this->balance;
    }
    public function getIsModerator(){
        return $this->isModerator;
    }
}