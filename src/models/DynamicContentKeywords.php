<?php
/**
 * Created by PhpStorm.
 * User: ivansal
 * Date: 28.08.15
 * Time: 17:25
 */

namespace DotPlant\Keywords\models;


use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%dynamic_content_keywords}}".
 * @property integer $id
 * @property integer $dynamic_content_id
 * @property string $keywords
 */
class DynamicContentKeywords extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%dynamic_content_keywords}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['keywords',], 'required', 'enableClientValidation' => false],
            [['dynamic_content_id'], 'integer'],
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
            'dynamic_content_id' => 'Dynamic content ID',
            'keywords' => 'Keywords',
        ];
    }
}