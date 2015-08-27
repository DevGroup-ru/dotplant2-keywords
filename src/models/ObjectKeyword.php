<?php

namespace DotPlant\Keywords\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%object_keyword}}".
 * @property integer $id
 * @property integer $object_id
 * @property integer $object_model_id
 * @property string $keywords
 */
class ObjectKeyword extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%object_keyword}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_id', 'object_model_id', 'keywords',], 'required', 'enableClientValidation' => false],
            [['object_id', 'object_model_id',], 'integer'],
            [['keywords'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'object_id' => 'Object ID',
            'object_model_id' => 'Object Model ID',
            'keywords' => 'Keywords',
        ];
    }
}