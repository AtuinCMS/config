<?php

namespace atuin\config\models;

use atuin\apps\models\AppConnections;
use yii;
use yii\base\Model;


/**
 * Class ModelConfig
 * @package atuin\engine\models
 */
class ModelConfig extends Model
{


    /**
     * Returns all the engine configuration for an Application id (frontend, backend, ...)
     * It will only return the configuration of the active Apps.
     *
     * @param String $section
     * @return array
     */
    public static function getActiveSectionConfigs($section)
    {
        /** @var Config[] $configList */
        // 1 - we get all the configs not connected to any App
        $configList = Config::find()->joinWith('appConnections', FALSE)->where(['reference_id' => NULL])
            ->andWhere(['or', ['section' => $section], ['section' => NULL]])->all();


        // 2 - then get all the configs connected to the active apps
        $configList = yii\helpers\ArrayHelper::merge(
            $configList,
            Config::find()->joinWith('app', FALSE)->where(['status' => 'active'])
                ->andWhere(['or', ['section' => $section], ['section' => NULL]])->all()
        );

        $array_config = [];

        foreach ($configList as $config) {

            $data = json_decode($config->data, TRUE);

            if (is_null($config->sub_group)) {
                $config_data = [$config->name => $data];
            } else {
                $config_data = [
                    $config->sub_group => [
                        $config->name => $data
                    ]
                ];
            }

            if (is_null($config->group)) {
                $sub_config = $config_data;
            } else {
                $sub_config = [$config->group => $config_data];
            }

            $array_config = yii\helpers\ArrayHelper::merge(
                $array_config,
                $sub_config);
        }

        return $array_config;
    }


    /**
     * Inserts config data into the database
     * Checks if there is another row with the same data to prevent data duplication
     *
     * @param null $section
     * @param $group
     * @param null $sub_group
     * @param $name
     * @param $data
     * @param bool $editable
     * @return Config|null|static
     */
    public static function addConfig($section = NULL, $group, $sub_group = NULL, $name, $data, $editable = TRUE)
    {

        $data = json_encode($data);

        $config_data = new Config();
        $config_data->section = $section;
        $config_data->group = $group;
        $config_data->sub_group = $sub_group;
        $config_data->name = $name;
        $config_data->data = $data;
        $config_data->editable = $editable;

        $checkDuplicateRecords = Config::findOne($config_data->toArray());

        if (is_null($checkDuplicateRecords)) {
            $config_data->save();
        } else {
            $config_data = $checkDuplicateRecords;
        }

        return $config_data;
    }

    /**
     * Deletes all config items assigned to the App
     *
     *
     * @param integer $appId
     * @throws \Exception
     */
    public static function deleteAppConfigItems($appId)
    {
        $configList = Config::find()->joinWith('appConnections', FALSE)->where(['app_id' => $appId])->all();

        foreach ($configList as $configItem) {
            $configItem->delete();
        }

        AppConnections::deleteAll(['app_id' => $appId, 'type' => Config::className()]);
    }

}