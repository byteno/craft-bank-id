<?php
/**
 * BankID plugin for Craft CMS 3.x
 *
 * BankID
 *
 * @link      https://byte.no
 * @copyright Copyright (c) 2019 Byte AS
 */

namespace byteas\bankid\variables;

use byteas\bankid\BankID;
use craft\helpers\Template;

/**
 * BankID Variable
 *
 * @author    Byte AS
 * @package   BankID
 * @since     1.0.0
 */
class BankIDVariable
{

    public function setupVariables()
    {
        $query = BankID::getInstance()->setup->getSetup();

        $result = [
            "client_id" => isset($query->client_id) ? $query->client_id : "",
            "client_secret" => isset($query->client_secret) ? $query->client_secret : "",
            "website_url" => isset($query->website_url) ? $query->website_url : "",
            "callback_endpoint" => isset($query->callback_endpoint) ? $query->callback_endpoint : "",
            "base_url" => isset($query->base_url) ? $query->base_url : "",
            "usergroup"=>isset($query->usergroup) ? $query->usergroup: "",
            "base_urls" => [
                "PRE-PROD" => "https://oidc-preprod.bankidapis.no/auth/realms/preprod/",
                "PROD" => "https://oidc.bankidapis.no/auth/realms/prod/",
                "CURRENT" => "https://oidc-current.bankidapis.no/auth/realms/current/"
            ]
        ];

        return $result;
    }

    public function settingsVariables()
    {

        $query = BankID::getInstance()->settings->getSettings();

        $result = [
            "redirect_after_login" => isset($query->redirect_after_login) ? $query->redirect_after_login : "",
            "allow_registration" => isset($query->allow_registration) ? $query->allow_registration : 1,
            "registration_url" => isset($query->registration_url) ? $query->registration_url : ""
        ];

        return $result;

    }

    public function loginButton()
    {
        $html = BankID::$plugin->bankIDService->getLoginButton();
        return Template::raw($html);
    }

}
