<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '库存信息';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-index" style="margin: 15px;">

    <?php $form = ActiveForm::begin();?>
    <div style="margin-top:20px;">
        <select id="date-range" name="date-range" class="">
            <option value="7">7天</option>
            <option value="30">30天</option>
            <option value="60">60天</option>
            <option value="90">90天</option>
        </select>
        <?= Html::input('text','keywords',Yii::$app->request->get('k'),['id'=>'keywords', 'class' => '', 'placeholder'=>'商家编码/名称']);?>
        <!-- <input type="submit" value="查询" class="btn btn-success"> -->
        <a id="btn_query" href="javascript:;" class="btn btn-success" target="_blank">查询</a>
        <?= Html::a('导入库存', ['import-stock'], ['class' => 'btn btn-success']) ?>
        <p class="pull-right">
            是否需要进货 = 库存数-日均出货量*(预计到货天数+1)，建议订货量 = 日均出货量*15
            
            <!-- <?= Html::a('导出', ['export'], ['class' => 'btn btn-success']) ?> -->
        </p>
    </div>
    <?php ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭分页
            'firstPageLabel'=>"«",
            'prevPageLabel'=>'‹',
            'nextPageLabel'=>'›',
            'lastPageLabel'=>'»',
        ],
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'header'=> '<a href="javascript:;">商家编码</a>',
                'value' => function ($data) {
                    return $data['code']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">商品名称</a>',
                'value' => function ($data) {
                    return $data['name']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">实际库存数</a>',
                'value' => function ($data) {
                    return $data['stock']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">到货天数</a>',
                'value' => function ($data) {
                    return $data['arrival_days']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">出货量</a>',
                'value' => function ($data) {
                    return $data['out_qty']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">日均销量</a>',
                'value' => function ($data) {
                    return round($data['out_qty_average'],1); 
                },
            ],
            [
                'header'=> '<a href="javascript:;">是否需要进货</a>',
                'format' => 'html',
                'value' => function ($data) {
                    $is_in = ($data['stock'] - $data['out_qty_average'] * ($data['arrival_days']+1)) > 0 ? '' : '<span style="color:red;font-weight:bold;">缺</span>';
                    return $is_in; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">建议进货量</a>',
                'value' => function ($data) {
                    return ($data['stock'] - $data['out_qty_average'] * ($data['arrival_days']+1)) > 0 ? '' : ceil($data['out_qty_average'] * 15); 
                    return '';
                },
            ]
        ],
        
    ]); ?>
</div>
<script>
    $(function(){    
        $('#date-range').val(<?= Yii::$app->request->get('d') ?>);    
        $("#btn_query").on('click',function(){
            
            var url = location.href;
            var k = $.trim($('#keywords').val());
            var d = $('#date-range').val();
            
            if(url.indexOf("&k=") > 0){
                url = changeUrlArg(url, "k", k);
            }else{
                url += "&k=" + k;
            }

            if(url.indexOf("&d=") > 0){
                url = changeUrlArg(url, "d", d);
            }else{
                url += "&d=" + d;
            }
            
            location.href = url;
        });

        var keywords = $('#keywords').val();
        if(keywords != ""){
            var regexp = new RegExp(keywords,"gim");
            var objs = $(".grid-view > table:contains('"+keywords+"')");
            objs.html(objs.html().replace(regexp,"<b style='background-color:yellow'>"+keywords+"</b>"));
        }
    });
</script>
