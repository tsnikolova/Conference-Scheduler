<?php
namespace Controllers;
use Framework\BaseController;
use Framework\Normalizer;
//use Models\BindingModels\ChangePasswordBindingModel;
use Models\BindingModels\LoginBindingModel;
use Models\BindingModels\RegisterBindingModel;
use Models\ViewModels\UserController\AllUsersViewModel;
use Models\ViewModels\UserController\ProfileViewModel;
use Models\ViewModels\UserController\User;
class UserController extends BaseController
{
    /**
     * @NotLogged
     * @param LoginBindingModel $model
     * @throws \Exception
     */
    public function login(LoginBindingModel $model)
    {
        $this->db->prepare("SELECT id, username
                                FROM users
                                WHERE username = ? AND password = ?",
            array($model->getUsername(), $model->getPassword()));
        $response = $this->db->execute()->fetchRowAssoc();
        if (!$response) {
            throw new \Exception('No user matching provided username and password!', 400);
        }
        $id = $response['id'];
        $username = $response['username'];
        $this->session->_login = $id;
        $this->session->_username = $model->getUsername();
        $this->session->escapedUsername = $username;
        $this->redirect('/');
    }
    /**
     * @Authorize error:("You are not logged in!")
     * @throws \Exception
     */
    public function logout()
    {
        $this->session->destroySession();
        $this->redirect('/');
    }
    /**
     * @NotLogged
     * @param RegisterBindingModel $model
     * @throws \Exception
     */
    public function register(RegisterBindingModel $model)
    {
        if ($model->getPassword() !== $model->getConfirm()) {
            throw new \Exception("Password don't match Confirm Password!", 400);
        }
        if (!preg_match('/^[\w]{3,15}$/', $model->getUsername())) {
            throw new \Exception("Invalid username format!", 400);
        }
        // Check for already registered with the same name
        $this->db->prepare("SELECT id
                                FROM users
                                WHERE username = ?",
            array($model->getUsername()));
        $response = $this->db->execute()->fetchRowAssoc();
        $id = $response['id'];
        if ($id !== null) {
            $username = $model->getUsername();
            throw new \Exception("Username '$username' already taken!", 400);
        }
        $this->db->prepare("INSERT
                            INTO users
                            (username, password, cash)
                            VALUES (?, ?, ?)",
            array(
                $model->getUsername(),
                $model->getPassword(),
                //$this->config->cart['initialCash']
            )
        )->execute();
        $loginBindingModel = new LoginBindingModel(array('username' => $model->getUsername(), 'password' => $model->getPassword()));
        // Work around to avoid double crypting passwords.
        $loginBindingModel->afterRegisterPasswordPass($model->getPassword());
        $this->login($loginBindingModel);
    }
    /**
     * @Get
     * @Route("user/{name:string}/profile")
     */
    public function profile()
    {
        $username = $this->input->getForDb(1);
        $this->db->prepare("SELECT id, isAdmin, Cash, isEditor, isModerator
                                FROM users
                                WHERE username = ?",
            array($username));
        $response = $this->db->execute()->fetchRowAssoc();
        if ($response === false) {
            throw new \Exception("No user found with name '$username'", 404);
        }
        $isAdmin = $response['isAdmin'];
        $isEditor = $response['isEditor'];
        $isModerator = $response['isModerator'];
        $balance = $response['Cash'];
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', new ProfileViewModel($username, $isAdmin, $balance, $isEditor, $isModerator));
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.userProfile');
    }
    /**
     * @Authorize
     * @Put
     * @Route("user/changePass")
     * @param ChangePasswordBindingModel $model
     * @throws \Exception
     */
    /*public function changePass(ChangePasswordBindingModel $model)
    {
        if ($model->getNewPassword() !== $model->getConfirm()) {
            throw new \Exception("Password don't match Confirm Password!", 400);
        }
        $username = $this->session->_username;
        $id = $this->session->_login;
        $this->db->prepare("SELECT id
                            FROM users
                            WHERE id = ? AND username = ? AND password = ?",
            array($id, $username, $model->getOldPassword()));
        $response = $this->db->execute()->fetchRowAssoc();
        if ($response) {
            $this->db->prepare("UPDATE users
                                SET password = ?
                                WHERE id = ? AND username = ? AND password = ?",
                array($model->getNewPassword(), $id, $username, $model->getOldPassword()));
            $this->db->execute();
            $this->redirect("/");
        } else {
            throw new \Exception("No user found matching those credentials!", 400);
        }
    }
    */
    /**
     * @Route("users/all/{start:int}/{end:int}")
     * @Get
     */
    public function allUsers()
    {
        $skip = $this->input->get(2);
        $take = $this->input->get(3) - $skip;
        $this->db->prepare("SELECT
                            username,isAdmin, isEditor, isModerator
                            FROM users
                            ORDER BY username
                            LIMIT {$take}
                            OFFSET {$skip}");
        $response = $this->db->execute()->fetchAllAssoc();
        $users = array();
        foreach ($response as $u) {
            $users[] = new User(
                $u['username'],
                Normalizer::normalize($u['isAdmin'], 'noescape|bool'),
                Normalizer::normalize($u['isEditor'], 'noescape|bool'),
                Normalizer::normalize($u['isModerator'], 'noescape|bool')
            );
        }
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', new AllUsersViewModel($users, $skip, $take + $skip));
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.home');
    }
}