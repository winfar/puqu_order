<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-import">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <p>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        <div class="form-group">
            <a href="resources/templet_goods.xls" class="btn btn-success" target="_blank">下载模板</a>
            <br><br>
            <!--<input type="file" name="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">-->
            <?= $form->field($model, 'file')->fileInput(['accept'=>'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) ?>
            <br>
            <?= Html::submitButton('导入', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php $form = ActiveForm::end(); ?>
    </p>
</div>
