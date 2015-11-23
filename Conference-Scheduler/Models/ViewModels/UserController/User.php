<?php
namespace Models\ViewModels\UserController;
class User
{
    private $username;
    private $isSiteAdmin;

    function __construct($username, $isSiteAdmin)
    {
        $this->username = $username;
        $this->isSiteAdmin = $isSiteAdmin;

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
    /**
     * @return mixed
     */

}