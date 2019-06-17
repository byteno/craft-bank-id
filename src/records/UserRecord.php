<?php

namespace byteas\bankid\records;

use craft\db\ActiveRecord;

class UserRecord extends ActiveRecord
{

    public static function tableName()
    {
        return '{{%bankid_userrecord}}';
    }

}
