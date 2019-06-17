<?php

namespace byteas\bankid\controllers;

use craft\web\Controller;

class HelpController extends Controller{


    public function actionIndex()
    {
        $this->renderTemplate("bank-id/help");
    }

}
