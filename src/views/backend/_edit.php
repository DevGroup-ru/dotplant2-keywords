<?php
use \app\backend\widgets\BackendWidget;
use yii\helpers\Html;


/**
 * @var $form \app\backend\components\ActiveForm
 * @var $keywordObject \DotPlant\Keywords\models\ObjectKeyword
 * @var $model \yii\base\Model
 */

?>
<div class="row">
    <article class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <?php BackendWidget::begin(
            ['title' => Yii::t('app', 'Keywords'), 'footer' => $this->blocks['submit']]
        ); ?>

        <?=$form->field(
            $keywordObject,
            'keywords',
            [
                'copyFrom' => [
                    "#" . Html::getInputId($model, 'name'),
                    "#" . Html::getInputId($model, 'title'),
                ]
            ]
        )?>

        <?php BackendWidget::end(); ?>
    </article>
</div>
<div class="clearfix"></div>
