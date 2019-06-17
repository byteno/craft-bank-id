<?php

namespace byteas\bankid\models;
use craft\base\Model;

class SetupModel extends Model{

    // VARIABLES
    public  $client_id,
        $client_secret,
        $website_url,
        $base_url,
        $usergroup;

    // VALIDATE FIELDS
    public function rules()
    {
        return[
            [['client_id', 'client_secret', 'website_url', 'base_url', 'usergroup'], 'required'],
            [['base_url', 'website_url'], 'url']
        ];
    }

}
