<?php

namespace byteas\bankid\controllers;

use byteas\bankid\BankID;
use Craft;
use craft\web\Controller;

class SetupController extends Controller{

    // RENDER SETUP PAGE
    public function actionIndex()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'].'/';
        $craftSiteUrl = $protocol . $domainName;

        $usergroups = Craft::$app->getUserGroups()->getAllGroups();

        $this->renderTemplate("bank-id/setup", [
            "craftSiteUrl"=>$craftSiteUrl,
            'usergroups'=>$usergroups
        ]);
    }

    // SAVE SETUP FORM
    public function actionSaveSetup()
    {

        $request = Craft::$app->getRequest();
        $params = $request->isPost ? $params = $request->post() : $params = $request->queryParams;
        $redirect = isset($params["redirect"]) ? $params["redirect"] : "/admin/bank-id/setup";

        if(!BankID::getInstance()->setup->saveSetup($params)){
            return $this->redirect($redirect);
        }

        Craft::$app->getSession()->setNotice('BankID setup has been saved! Ready to use.');
        return $this->redirect($params["redirect"]);

    }

    // SAVE WEBSITE URL
    public function actionSaveWebsiteUrl()
    {

        $request = Craft::$app->getRequest();
        $params = $request->isPost ? $params = $request->post() : $params = $request->queryParams;
        $redirect = isset($params["redirect"]) ? $params["redirect"] : "/admin/bank-id/setup";

        if(!BankID::getInstance()->setup->saveWebsiteUrl($params)){
            return $this->redirect($redirect);
        }

        Craft::$app->getSession()->setNotice('Website URL saved!');
        return $this->redirect($redirect);

    }

}
