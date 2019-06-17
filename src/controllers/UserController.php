<?php

namespace byteas\bankid\controllers;

use byteas\bankid\BankID;
use Craft;
use craft\web\Controller;
use craft\web\View;

class UserController extends Controller{

    protected $allowAnonymous = true;

    // POST - REGISTER
    public function actionRegister()
    {
        $request = Craft::$app->getRequest();
        $params = $request->isPost ? $params = $request->post() : $params = $request->queryParams;
        //$redirect = isset($params["redirect"]) ? $params["redirect"] : "/bank-id/user/registration";

        $result = BankID::getInstance()->user->createUser($params);

        if($result["error"]){
            if(isset($result["redirect_to_front"])){
                Craft::$app->getSession()->setError($result["message"]);
                return $this->redirect("/");
            }
            Craft::$app->getSession()->setError($result["message"]);
            return $this->redirect("/bank-id/user/registration");
        }

        return $this->redirect("/");

    }

    // SHOW REGISTRATION FORM
    public function actionRegistration()
    {

        $first_name = BankID::getInstance()->session->getFirstName();
        $last_name = BankID::getInstance()->session->getLastName();
        $sub = BankID::getInstance()->session->getBankIdSub();

        if(empty($first_name) || empty($last_name) || empty($sub)){
           return $this->_renderErrorTemplate("Session expired");
        }

        return $this->_renderRegistrationtemplate($first_name, $last_name);

    }

    // RENDER REGISTRATION TEMPLATE
    private function _renderRegistrationtemplate($firstName, $lastName)
    {
        $oldMode = Craft::$app->view->getTemplateMode();
        Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_CP);

        $html = Craft::$app->view->renderTemplate(
            'bank-id/registration.twig',
            ["first_name"=>$firstName, "last_name"=>$lastName]
        );

        Craft::$app->view->setTemplateMode($oldMode);

        return $html;
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
