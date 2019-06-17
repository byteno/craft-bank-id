<?php

namespace byteas\bankid\controllers;

use byteas\bankid\BankID;
use Craft;
use craft\web\Controller;

class SettingsController extends Controller{

    // RENDER SETTINGS PAGE
    public function actionIndex()
    {
        $this->renderTemplate("bank-id/settings");
    }

    public function actionSaveSettings()
    {

        $request = Craft::$app->getRequest();
        $params = $request->isPost ? $params = $request->post() : $params = $request->queryParams;
        $redirect = isset($params["redirect"]) ? $params["redirect"] : "/admin/bank-id";

        if(!BankID::getInstance()->settings->saveSettings($params)){
            return $this->redirect($redirect);
        }

        Craft::$app->getSession()->setNotice('BankID settings has been saved!');
        return $this->redirect($params["redirect"]);

    }

}
