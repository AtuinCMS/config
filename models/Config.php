<?php

namespace atuin\config\models;

use atuin\apps\models\App;
use atuin\apps\models\AppConnections;
use yii\db\ActiveRecord;


/**
 * Class Config
 * @package atuin\engine\models
 *
 * @property int $id
 *
 * defines the application section -> backend, frontend, etc...
 * @property string $section
 *
 * config type for the yii2 application class -> modules, components, etc...
 * @property string $group
 *
 * class / module linked to the config data (user, mail, etc...)
 * @property string $sub_group
 * @property string $name
 * @property string $data
 *
 * editable = true shows in the form configuration this config data
 * @property int $editable
 *
 */
class Config extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    static function tableName()
    {
        return 'site_config';
    }


    /**
     * Returns all the connections of the Apps in the AppConnections Active Record
     *
     * Useful to retrieve all the configs, pages and extra data that Apps have
     *
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAppConnections()
    {
        return $this->hasMany(AppConnections::className(), ['reference_id' => 'id']);
    }


    /**
     * Retrieves all the Configs assigned to the filtered Apps using AppConnections
     * as junction table.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getApp()
    {
        return $this->hasOne(App::className(), ['id' => 'app_id'])->
        via('appConnections', function ($query)
        {
            $query->where(['type' => Config::className()]);
        });
    }


}