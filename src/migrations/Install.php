<?php
/**
 * BankID plugin for Craft CMS 3.x
 *
 * BankID
 *
 * @link      https://byte.no
 * @copyright Copyright (c) 2019 Byte AS
 */

namespace byteas\bankid\migrations;

use byteas\bankid\records\BankIDRecord;
use byteas\bankid\records\SettingsRecord;
use byteas\bankid\records\SetupRecord;
use byteas\bankid\records\UserRecord;
use Craft;
use craft\config\DbConfig;
use craft\db\Migration;

/**
 * BankID Install Migration
 *
 * @author    Byte AS
 * @package   BankID
 * @since     1.0.0
 */
class Install extends Migration
{

    public $driver;

    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            $this->createIndexes();
            $this->addForeignKeys();
            $this->addDefaultRows();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        return true;
    }

    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->removeTables();

        return true;
    }

    protected function createTables()
    {
        $tablesCreated = false;

        // Base
        $tableSchema = Craft::$app->db->schema->getTableSchema(BankIDRecord::tableName());
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                BankIDRecord::tableName(),
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                    'siteId' => $this->integer()->notNull(),
                ]
            );
        }

        // Setup
        $tableSchema = Craft::$app->db->schema->getTableSchema(SetupRecord::tableName());
        if($tableSchema === null){
            $this->createTable(
                SetupRecord::tableName() ,[
                    'id' => $this->integer(1),
                    'uid' => $this->uid(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'client_id' => $this->string(255)->notNull()->defaultValue(''),
                    'client_secret' => $this->string(255)->notNull()->defaultValue(''),
                    'website_url' => $this->string(255)->notNull()->defaultValue(''),
                    'callback_endpoint' => $this->string(255)->notNull()->defaultValue(''),
                    'base_url' => $this->string(255)->notNull()->defaultValue(''),
                    'usergroup'=>$this->integer(2)->null()
                ]
            );
        }else{
            $tablesCreated = false;
        }

        // Settings
        $tableSchema = Craft::$app->db->schema->getTableSchema(SettingsRecord::tableName());
        if($tableSchema === null){
            $this->createTable(
                SettingsRecord::tableName() ,[
                    'id' => $this->integer(1),
                    'uid' => $this->uid(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'allow_registration' => $this->integer(1)->notNull()->defaultValue(1),
                    'redirect_after_login' => $this->string(120)->defaultValue(""),
                    'registration_url' => $this->string(120)->null()
                ]
            );
        }else{
            $tablesCreated = false;
        }

        // Users
        $tableSchema = Craft::$app->db->schema->getTableSchema(UserRecord::tableName());
        if($tableSchema === null){
            $this->createTable(
                UserRecord::tableName() ,[
                    'id' => $this->integer(1),
                    'uid' => $this->uid(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'user_id'=>$this->integer(4)->defaultValue(0)->notNull(),
                    'bankid_sub'=>$this->string(48)->defaultValue("")->null()
                ]
            );
        }else{
            $tablesCreated = false;
        }

        return $tablesCreated;
    }

    protected function createIndexes()
    {

        $this->createIndex(
            $this->db->getIndexName(
                BankIDRecord::tableName(),
                ['id'],
                true
            ),
            BankIDRecord::tableName(),
            ['id'],
            true
        );

        $this->createIndex(
            $this->db->getIndexName(
                UserRecord::tableName(),
                ['id'],
                true
            ),
            UserRecord::tableName(),
            ['id'],
            true
        );

        // Additional commands depending on the db driver
        switch ($this->driver) {
            case DbConfig::DRIVER_MYSQL:
                break;
            case DbConfig::DRIVER_PGSQL:
                break;
        }
    }

    protected function addForeignKeys()
    {
        $this->addForeignKey(
            $this->db->getForeignKeyName(BankIDRecord::tableName(), 'siteId'),
            BankIDRecord::tableName(),
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    protected function addDefaultRows()
    {
        $this->insert(SetupRecord::tableName(),['id'=>1]);
        $this->insert(SettingsRecord::tableName(),['id'=>1]);
    }

    // Drop tables
    protected function removeTables()
    {
        $this->dropTableIfExists(BankIDRecord::tableName());
        $this->dropTableIfExists(SetupRecord::tableName());
        $this->dropTableIfExists(SettingsRecord::tableName());
        $this->dropTableIfExists(UserRecord::tableName());
    }
}
