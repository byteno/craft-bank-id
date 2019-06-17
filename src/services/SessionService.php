<?php

namespace byteas\bankid\services;

use yii\base\Component;

class SessionService extends Component{

    public function setUserId($id)
    {
        $_SESSION["bank_id"]["user_id"] = $id;
    }
    public function setRedirectUrl($url)
    {
        $_SESSION["bank_id"]["redirect_url"] = $url;
    }
    public function setCallbackType($type)
    {
        $_SESSION["bank_id"]["callback_type"] = $type;
    }
    public function setEmail($email)
    {
        $_SESSION["bank_id"]["email"] = $email;
    }
    public function setBankIdSub($sub)
    {
        $_SESSION["bank_id"]["sub"] = $sub;
    }
    public function setFirstName($name)
    {
        $_SESSION["bank_id"]["first_name"] = $name;
    }
    public function setLastName($name)
    {
        $_SESSION["bank_id"]["last_name"] = $name;
    }

    public function getUserId()
    {
        return isset($_SESSION["bank_id"]["user_id"]) ? $_SESSION["bank_id"]["user_id"] : null;
    }
    public function getRedirectUrl()
    {
        return isset($_SESSION["bank_id"]["redirect_url"]) ? $_SESSION["bank_id"]["redirect_url"] : null;
    }
    public function getCallbackType()
    {
        return isset($_SESSION["bank_id"]["callback_type"]) ? $_SESSION["bank_id"]["callback_type"] : null;
    }
    public function getEmail()
    {
        return isset($_SESSION["bank_id"]["email"]) ? $_SESSION["bank_id"]["email"] : null;
    }
    public function getBankIdSub()
    {
        return isset($_SESSION["bank_id"]["sub"]) ? $_SESSION["bank_id"]["sub"] : null;
    }
    public function getFirstName()
    {
        return isset($_SESSION["bank_id"]["first_name"]) ? $_SESSION["bank_id"]["first_name"] : null;
    }
    public function getLastName()
    {
        return isset($_SESSION["bank_id"]["last_name"]) ? $_SESSION["bank_id"]["last_name"] : null;
    }

    public function clearBankIdSessions()
    {
        unset($_SESSION["bank_id"]);
    }

}
