<?php

namespace byteas\bankid\controllers;

use byteas\bankid\BankID;
use Craft;
use craft\web\Controller;
use craft\web\View;

class OicdController extends Controller{

    public $enableCsrfValidation = false;
    protected $allowAnonymous = true;

    // START BANKID LOGIN
    public function actionBankIdLogin()
    {

        // Check if setup is completed
        if(!$this->_verifySetupComplete()){
            echo $this->_renderErrorTemplate("BankID setup is not completed");
            return false;
        }

        BankID::getInstance()->oicd->doBankIdLogin();

        return true;

    }

    // BANKID CALLBACK
    public function actionCallback()
    {

        $setup = BankID::getInstance()->setup->getSetup();
        if(!isset($setup["client_id"]) || empty($setup["client_id"])) {
            echo $this->_renderErrorTemplate("BankID setup is not completed");
            return false;
        }

        $result = BankID::getInstance()->oicd->handleCallback($setup);

        if($result["error"]){
            return $this->_renderErrorTemplate($result["message"]);
        }else if(isset($result["show_registration"])){
            return $this->redirect("/bank-id/user/registration");
        }

        $settings = BankID::getInstance()->settings->getSettings();
        $redirect = empty($settings["redirect_after_login"]) ? "/" : $settings["redirect_after_login"];

        return $this->redirect($redirect);

    }

    private function _verifySetupComplete()
    {
        $setup = BankID::getInstance()->setup->getSetup();
        if(!isset($setup["client_id"]) || empty($setup["client_id"])) {
            return false;
        }
        return true;
    }

    // RENDER ERROR TEMPLATE
    private function _renderErrorTemplate($errorMessage)
    {

        $oldMode = Craft::$app->view->getTemplateMode();
        Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

        $html = Craft::$app->view->renderTemplate(
            'bank-id/error.twig',
            ["errorMessage"=>$errorMessage]
        );

        Craft::$app->view->setTemplateMode($oldMode);

        return $html;

    }

}
