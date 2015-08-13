<?php


use yii\db\Schema;


class m000000_000002_atuin_config_migration extends \yii\db\Migration
{

    protected function configsTableName()
    {
        return \atuin\config\models\Config::tableName();
    }

    public function safeUp()
    {
        $tableOptions = null;
        if (Yii::$app->db->driverName === 'mysql')
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        
        /**
         * Configs table that will store Atuin Config data.
         * Linked to Apps table.
         */
        $this->createTable($this->configsTableName(), [
            'id' => Schema::TYPE_PK,
            // defines the application section -> backend, frontend, etc...
            'section' => Schema::TYPE_STRING . '(255)',
            // config type for the yii2 application class -> modules, components, etc...
            'group' => Schema::TYPE_STRING . " DEFAULT NULL",
            // class / module linked to the config data (user, mail, etc...)
            'sub_group' => Schema::TYPE_STRING . '(255) DEFAULT NULL',
            'name' => Schema::TYPE_STRING . '(255) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            // editable = true shows in the form configuration this config data
            'editable' => Schema::TYPE_SMALLINT . '(1) NOT NULL DEFAULT 1',
        ], $tableOptions);

        
        // add indexes for performance optimization
        $this->createIndex('{{%site_config_section%}}', $this->configsTableName(), ['section']);
        
        // add indexes for performance optimization
        $this->createIndex('{{%site_config_section_editable%}}', $this->configsTableName(), ['section', 'editable']);
        

    }
}