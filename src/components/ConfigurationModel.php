<?php

namespace DotPlant\Keywords\components;


use app\modules\config\models\BaseConfigurationModel;

class ConfigurationModel extends BaseConfigurationModel
{
    /**
     * Fills model attributes with default values
     * @return void
     */
    public function defaultValues()
    {

    }

    /**
     * Returns array of module configuration that should be stored in application config.
     * Array should be ready to merge in app config.
     * Used both for web only.
     * @return array
     */
    public function webApplicationAttributes()
    {
        return [
        	'modules' => [
                'Keywords' => [
                    'class' => 'DotPlant\Keywords\Module',
                ],
            ],
            'bootstrap' => [
                'Keywords' => 'Keywords',
            ],
        ];
    }

    /**
     * Returns array of module configuration that should be stored in application config.
     * Array should be ready to merge in app config.
     * Used both for console only.
     * @return array
     */
    public function consoleApplicationAttributes()
    {
        return [];
    }

    /**
     * Returns array of module configuration that should be stored in application config.
     * Array should be ready to merge in app config.
     * Used both for web and console.
     * @return array
     */
    public function commonApplicationAttributes()
    {
        return [
            
        ];
    }

    /**
     * Returns array of key=>values for configuration.
     * @return mixed
     */
    public function keyValueAttributes()
    {
        return [];
    }

    /**
     * Returns array of aliases that should be set in common config
     * @return array
     */
    public function aliases()
    {
        return [
            '@keywords' => realpath(dirname(__FILE__) . '/../'),
        ];
    }
}