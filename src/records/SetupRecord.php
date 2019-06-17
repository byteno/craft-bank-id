<?php

namespace byteas\bankid\records;

use craft\db\ActiveRecord;

class SetupRecord extends ActiveRecord
{

    public static function primaryKey()
    {
        return ['id'];
    }

    public static function tableName()
    {
        return '{{%bankid_setuprecord}}';
    }

}
