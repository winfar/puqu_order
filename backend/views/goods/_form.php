<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Goods */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="goods-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig'=>[
            // 'template'=> "{label}\n<div class=\"col-sm-8\">{input}</div>\n{error}"
        ]
    ]);
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <!-- <?= $form->field($model, 'barcode')->textInput(['maxlength' => true]) ?> -->

    <!-- <?= $form->field($model, 'category_id')->textInput() ?> -->

    <?= $form->field($model, 'category_name')->textInput(['maxlength' => true]) ?>

    <!-- <?= $form->field($model, 'brand')->textInput(['maxlength' => true]) ?> -->

    <!-- <?= $form->field($model, 'supplier')->textInput(['maxlength' => true]) ?> -->

    <?= $form->field($model, 'specification')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stock')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'stock_position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'arrival_days')->textInput(['maxlength' => true])->hint('到货天数值为0时则读取公共定义的天数') ?>

    <?= $form->field($model, 'clear')->checkBox() ?>

    <?= $form->field($model, 'status')->checkBox() ?>

    <!-- <?= $form->field($model, 'create_time')->textInput(['maxlength' => true]) ?> -->

    <!-- <?= $form->field($model, 'update_time')->textInput(['maxlength' => true]) ?> -->

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
