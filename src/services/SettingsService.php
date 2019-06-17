<?php

namespace byteas\bankid\services;

use byteas\bankid\models\SettingsModel;
use byteas\bankid\records\SettingsRecord;
use Craft;
use DateTime;
use yii\base\Component;

class SettingsService extends Component
{

    // GET SETTINGS FROM DB
    public function getSettings()
    {
        return SettingsRecord::find()->one();
    }

    // SAVE SETTINGS TO DB
    public function saveSettings($params)
    {

        // SettingsModel param assignment
        $settingsModel = new SettingsModel();
        $settingsModel->allow_registration = isset($params["allow_registration"]) ? $params["allow_registration"] : null;
        $settingsModel->redirect_after_login = isset($params["redirect_after_login"]) ? $params["redirect_after_login"] : null;

        // Validate
        if(!$settingsModel->validate()){
            Craft::$app->getSession()->setError("Form validation failed. Empty or incorrect values");
            return false;
        }

        $settingsRecord = $this->getSettings();
        $currentDate = new DateTime();

        $settingsRecord->allow_registration = $params["allow_registration"];
        $settingsRecord->dateUpdated = $currentDate;
        $settingsRecord->redirect_after_login = $params["redirect_after_login"];

        if(!$settingsRecord->save()){
            Craft::$app->getSession()->setError("Database error");
            return false;
        }

        return true;

    }

}
