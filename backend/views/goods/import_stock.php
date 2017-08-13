<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商品库存导入';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-import-stock">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        <div class="form-group">
            <a href="resources/templet_goods_stock.xls" class="btn btn-success" target="_blank">下载模板</a>
            <br><br>
            <!--<input type="file" name="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">-->
            <?= $form->field($model_upload, 'file')->fileInput(['accept'=>'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) ?>
            <br>
            <?= Html::input('text','date',date('Y-m-d',time() - 60 * 60 * 24),['id'=>'date', 'class' => '', 'placeholder'=>'导入库存日期']);?>
            <?= empty($error_msg) ? '' : $error_msg ?>
            <br><br>
            <?= Html::submitButton('导入商品库存', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php $form = ActiveForm::end(); ?>
    </p>
</div>
