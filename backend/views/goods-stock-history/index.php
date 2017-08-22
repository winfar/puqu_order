<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '日销量导入历史';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-stock-history-index" >

    <?php $form = ActiveForm::begin();?>
    <div style="margin-top:20px;">
        <!-- <?= Html::input('text','keywords',Yii::$app->request->get('k'),['id'=>'keywords', 'class' => '', 'placeholder'=>'商家编码/名称']);?> -->
        <?= Html::input('text','date',empty(Yii::$app->request->get('d')) ? date('Y-m-d',time()) : Yii::$app->request->get('d'),['id'=>'date', 'class' => 'datepicker', 'placeholder'=>'导入库存日期', 'data-provide'=>'datepicker','data-date-format'=>'yyyy-mm-dd']) ?>
        <a id="btn_query" href="javascript:;" class="btn btn-success">查询</a>
        <p class="pull-right">
        </p>
    </div>
    <?php ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'grid-view table-responsive'],
        'pager'=>[
            //'options'=>['class'=>'hidden']//关闭分页
            'firstPageLabel'=>"«",
            'prevPageLabel'=>'‹',
            'nextPageLabel'=>'›',
            'lastPageLabel'=>'»',
        ],
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
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
                'header'=> '<a href="javascript:;">日销量</a>',
                'value' => function ($data) {
                    return $data['stock']; 
                },
            ],
            [
                'header'=> '<a href="javascript:;">创建时间</a>',
                'attribute' => 'create_time',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            [
                'header'=> '<a href="javascript:;">更新时间</a>',
                'attribute' => 'update_time',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            [
                'header'=> '<a href="javascript:;">导入日期</a>',
                'attribute' => 'stock_date',
                'format' => ['date', 'php:Y-m-d']
            ],
        ],
    ]); ?>
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

        $("#btn_query").on('click',function(){
            
            var url = location.href;

            // var k = $.trim($('#keywords').val());
            // if(url.indexOf("&k=") > 0){
            //     url = changeUrlArg(url, "k", k);
            // }else{
            //     url += "&k=" + k;
            // }

            var d = $.trim($('#date').val());
            if(url.indexOf("&d=") > 0){
                url = changeUrlArg(url, "d", d);
            }else{
                url += "&d=" + d;
            }
            
            location.href = url;
        });

        // var keywords = $('#keywords').val();
        // if(keywords != ""){
        //     var regexp = new RegExp(keywords,"gim");
        //     var objs = $(".grid-view > table:contains('"+keywords+"')");
        //     objs.html(objs.html().replace(regexp,"<b style='background-color:yellow'>"+keywords+"</b>"));
        // }
    });
</script>
<?php $this->endBlock(); ?>
