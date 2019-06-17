<?php

namespace byteas\bankid\services;

use byteas\bankid\BankID;
use byteas\bankid\records\UserRecord;
use Craft;
use Jumbojett\OpenIDConnectClient;
use yii\base\Component;

class OicdService extends Component
{

    // DO BANKID LOGIN
    public function doBankIdLogin()
    {

        $callbackType = BankID::getInstance()->session->getCallbackType();
        if(empty($callbackType)){
            BankID::getInstance()->session->setCallbackType("login");
        }

        $settings = $this->_getSetup();

        $redirectUrl = $settings["website_url"] . $settings["callback_endpoint"];

        $oidc = $this->_getOIDClient($settings);
        $oidc->setRedirectURL($redirectUrl);

        $oidc->addScope('openid profile');
        $oidc->authenticate();
        exit;
    }

    // HANDLE CALLBACK
    public function handleCallback()
    {

        $settings = $this->_getSetup();
        $oidc = $this->_getOIDClient($settings);

        $redirectUrl = $settings["website_url"] . $settings["callback_endpoint"];
        $oidc->setRedirectURL($redirectUrl);

        $oidc->authenticate();
        $res = $oidc->getIdTokenPayload();

        if(!is_object($res)){
            return ["error"=>true, "message"=>"Unknown error #10"];
        }

        if(!isset($res->sub) || empty($res->sub)){
            return ["error"=>true, "message"=>"Unknown error #11"];
        }

        // Get type of callback from session
        $callbackType = BankID::getInstance()->session->getCallbackType();

        // Check if empty
        if(empty($callbackType)){
            return ["error"=>true, "message"=>"Unknown error #12"];
        }

        // Point type to function
        if($callbackType === "login"){
            $result = $this->_callbackLogin($res);
        }else if($callbackType === "validate_email"){
            $result = $this->_callbackValidate($res);
        }else{
            $result = ["error"=>true, "message"=>"Unknown error #13"];
        }

        return $result;

    }

    // GET OIDC CLIENT
    private function _getOIDClient($settings)
    {
        return new OpenIDConnectClient(
            $settings["base_url"],
            $settings["client_id"],
            $settings["client_secret"]
        );
    }

    // CALLBACK - LOGIN
    private function _callbackLogin($res)
    {

        $result = UserRecord::find()->where(['bankid_sub' => $res->sub])->one();
        if(empty($result)){

            $settings = $this->_getSettings();

            // Redirect to register if allowed
            if($settings->allow_registration){
                BankID::getInstance()->session->setBankIdSub($res->sub);
                BankID::getInstance()->session->setFirstName($res->given_name);
                BankID::getInstance()->session->setLastName($res->family_name);
                return ["error"=>false, "show_registration" => true, "first_name"=>$res->given_name, "last_name"=>$res->family_name];
            }

            return ["error"=>true, "message"=>"User not found"];
        }

        $result = BankID::getInstance()->user->loginUserById($result->user_id);

        return $result;

    }

    // CALLBACK - EMAIL VALIDATE NEW USER
    private function _callbackValidate($res)
    {

        $result = UserRecord::find()->where(['bankid_sub' => $res->sub])->one();
        if(!empty($result)){
            return ["error"=>true, "message"=>"User already registered"];
        }

        $user_id = BankID::getInstance()->session->getUserId();

        if(empty($user_id)){
            return ["error"=>true, "message"=>"Session expired"];
        }

        $bankIdUser = new UserRecord();
        $bankIdUser->user_id = $user_id;
        $bankIdUser->bankid_sub = $res->sub;
        $bankIdUser->save();

        $user = Craft::$app->users->getUserById($user_id);
        Craft::$app->getUsers()->activateUser($user);

        Craft::$app->user->loginByUserId($user_id);

        BankID::getInstance()->session->clearBankIdSessions();

        return ["error"=>false];

    }

    // GET SETUP VARIABLES
    private function _getSetup()
    {
        return BankID::getInstance()->setup->getSetup();
    }

    // GET SETTINGS VARIABLES
    private function _getSettings()
    {
        return BankID::getInstance()->settings->getSettings();
    }

}
