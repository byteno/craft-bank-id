<?php

namespace byteas\bankid\models;
use craft\base\Model;

class SettingsModel extends Model{

    // VARIABLES
    public  $allow_registration,
            $redirect_after_login;

    // VALIDATE FIELDS
    public function rules()
    {
        return[
            [['allow_registration'], 'required'],
        ];
    }

}
