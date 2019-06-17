<?php

namespace byteas\bankid\services;

use byteas\bankid\BankID;
use byteas\bankid\records\UserRecord;
use Craft;
use yii\base\Component;
use craft\elements\User;

class UserService extends Component{

    // BEFORE VALIDATE EMAIL EVENT
    public function handleBeforeVerifyEmail($event)
    {
        // If already completed verification
        if(isset($_SESSION["bank_id"]["did_validate"]) && $_SESSION["bank_id"]["did_validate"] === true){
            return ["error"=>false];
        }

        // Get UserID from Craft
        $userId = isset($event->user->id) ? $event->user->id : null;
        if(empty($userId)){
            return ["error"=>true, "message"=>"UserID not found. #20"];
        }

        // Get current URL
        $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        // Set session variables
        BankID::getInstance()->session->setUserId($event->user->id);
        BankID::getInstance()->session->setRedirectUrl($redirect_url);
        BankID::getInstance()->session->setCallbackType("validate_email");

        // Redirect to BankID Login
        header("Location: /bank-id/bank-id-login");
        exit;

    }

    // CREATE NEW USER
    public function createUser($params)
    {

        $email = isset($params["email"]) ? $params["email"] : null;
        $first_name = BankID::getInstance()->session->getFirstName();
        $last_name = BankID::getInstance()->session->getLastName();
        $sub = BankID::getInstance()->session->getBankIdSub();

        if(empty($email)){
            return ["error"=>true, "message"=>"Email was empty"];
        }

        $email_check = str_replace(['Æ', 'Ø', 'Å', 'æ', 'ø', 'å'], 'a', $email);
        if(filter_var($email_check,FILTER_VALIDATE_EMAIL)==false){
            return ["error"=>true, "message"=>"Invalid email"];
        }

        if(empty($first_name) || empty($last_name) || empty($sub)){
            return ["error"=>true, "message"=>"Session expired", "redirect_to_front"=>true];
        }

        $user = Craft::$app->users->getUserByUsernameOrEmail($email);
        if(!empty($user)){
            return ["error"=>true, "message"=>"User already exists"];
        }

        $setup = BankID::getInstance()->setup->getSetup();
        $usergroup = $setup->usergroup;

        $user = new User();
        $user->pending = true;
        $user->username = $email;
        $user->firstName = $first_name;
        $user->lastName = $last_name;
        $user->email = $email;
        $user->passwordResetRequired = false;
        $user->validate(null, false);

        Craft::$app->getElements()->saveElement($user, false);
        Craft::$app->users->assignUserToGroups($user->id, [$usergroup]);
        //Craft::$app->getUsers()->sendActivationEmail($user);
        Craft::$app->getUsers()->activateUser($user);

        $user_id = $user->id;

        $bankIdUser = new UserRecord();
        $bankIdUser->user_id = $user_id;
        $bankIdUser->bankid_sub = $sub;
        $bankIdUser->save();

        Craft::$app->user->loginByUserId($user_id);

        BankID::getInstance()->session->clearBankIdSessions();

        return ["error"=>false];

    }

    // LOG IN USER BY ID
    public function loginUserById($id)
    {
        $user = Craft::$app->getUsers()->getUserById($id);
        $generalConfig = Craft::$app->getConfig()->getGeneral();
        $duration = $generalConfig->userSessionDuration;

        if (!Craft::$app->getUser()->login($user, $duration)) {
            return ["error"=>true, "message"=>"User does not exist"];
        }

        return ["error"=>false];

    }

}
