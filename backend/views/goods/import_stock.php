<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-import-stock">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
        <div class="form-group">
            <a href="resources/templet_goods_stock.xls" class="btn btn-success" target="_blank">下载模板</a>
            <br><br>
            <?= Html::input('text','date',date('Y-m-d',time() - 60 * 60 * 24),['id'=>'date', 'class' => 'datepicker', 'placeholder'=>'导入库存日期', 'data-provide'=>'datepicker','data-date-format'=>'yyyy-mm-dd']) ?>
            <span class="alert text-danger"><?= empty($error_msg) ? '' : $error_msg ?></span>
            <br><br>
            <!--<input type="file" name="file" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">-->
            <?= $form->field($model_upload, 'file')->fileInput(['accept'=>'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']) ?>
            <br>
            <?= Html::submitButton('导入', ['class' => 'btn btn-primary']) ?>
        </div>
        <?php $form = ActiveForm::end(); ?>
    </p>
</div>
<?php $this->beginBlock('footer');  ?>
<script>
    $(function(){
        $('.datepicker').datepicker({
            language: 'zh-CN',
            autoclose: true,
            todayHighlight: true,
            // clearBtn: true,//清除按钮
            // todayBtn: true,//今日按钮
            format: "yyyy-mm-dd"//日期格式
        });
    })
</script>
<?php $this->endBlock(); ?>
