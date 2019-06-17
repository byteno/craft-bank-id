<?php

namespace byteas\bankid\services;

use byteas\bankid\models\SetupModel;
use byteas\bankid\records\SetupRecord;
use Craft;
use DateTime;
use yii\base\Component;

class SetupService extends Component
{

    private $callbackEndpointUri = 'bank-id/callback';

    // GET SETUP FROM DB
    public function getSetup()
    {
        return SetupRecord::find()->one();
    }

    // SAVE WEBSITE URL (STEP 1)
    public function saveWebsiteUrl($params)
    {

        $params["website_url"] = isset($params["website_url"]) ? $params["website_url"] : "";

        if (empty($params["website_url"])) {
            Craft::$app->getSession()->setError("URL cannot be empty");
            return false;
        }

        if (!filter_var($params["website_url"], FILTER_VALIDATE_URL)) {
            Craft::$app->getSession()->setError("Invalid URL");
            return false;
        }

        // Website URL fix
        $params["website_url"] = (substr($params["website_url"], -1) === "/") ? $params["website_url"] : "/" . $params["website_url"];

        $setupRecord = $this->getSetup();
        $currentDate = new DateTime();

        $setupRecord->website_url = $params["website_url"];
        $setupRecord->dateUpdated = $currentDate;
        $setupRecord->callback_endpoint = $this->callbackEndpointUri;
        if(!$setupRecord->save()){
            Craft::$app->getSession()->setError("Database Error");
            return false;
        }

        return true;

    }

    // SAVE SETTINGS (STEP 2)
    public function saveSetup($params)
    {

        // SetupModel param assignment
        $setupModel = new SetupModel();
        $setupModel->client_id = isset($params["client_id"]) ? $params["client_id"] : null;
        $setupModel->client_secret = isset($params["client_secret"]) ? $params["client_secret"] : null;
        $setupModel->website_url = isset($params["website_url"]) ? $params["website_url"] : "";
        $setupModel->base_url = isset($params["base_url"]) ? $params["base_url"] : null;
        $setupModel->usergroup = isset($params["usergroup"]) ? $params["usergroup"] : null;

        // Validate
        if(!$setupModel->validate()){
            Craft::$app->getSession()->setError("Form validation failed. Empty or incorrect values");
            return false;
        }

        // Website URL fix
        $params["website_url"] = (substr($params["website_url"], -1) === "/") ? $params["website_url"] : "/" . $params["website_url"];

        // Update record
        $setupRecord = $this->getSetup();
        $currentDate = new DateTime();

        $setupRecord->client_id = $params["client_id"];
        $setupRecord->dateUpdated = $currentDate;
        $setupRecord->client_secret = $params["client_secret"];
        $setupRecord->website_url = $params["website_url"];
        $setupRecord->callback_endpoint = $this->callbackEndpointUri;
        $setupRecord->base_url = $params["base_url"];
        $setupRecord->usergroup = $params["usergroup"];

        if(!$setupRecord->save()){
            Craft::$app->getSession()->setError("Database error");
            return false;
        }

        return true;

    }
}
