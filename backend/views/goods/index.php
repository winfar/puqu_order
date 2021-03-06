<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-index" >

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?php $form = ActiveForm::begin();?>
    <div style="margin-top:20px;">
        <label for="clear">销售状态：</label>
        <select id="clear" name="clear" class="" style="width:100px;padding:1px 0;">
            <option value="" selected >所有</option>
            <option value="0">在售</option>
            <option value="1">清库</option>
        </select>
        <?= Html::input('text','keywords',Yii::$app->request->get('k'),['id'=>'keywords', 'class' => '', 'placeholder'=>'商家编码/名称']);?>
        <a id="btn_query" href="javascript:;" class="btn btn-success" target="_blank">查询</a>
        <?= Html::a('导入商品', ['import'], ['class' => 'btn btn-success']) ?>
        <span class="pull-right">
            <?= Html::a('添加商品', ['create'], ['class' => 'btn btn-success']) ?>
        </span>
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
            'id',
            'code',
            'name',
            // 'barcode',
            // 'category_id',
            'category_name',
            // 'brand',
            // 'supplier',
            // 'specification',
            'price',
            'stock',
            'stock_position',
            [
                'header'=> '<a href="javascript:;">是否清库</a>',
                'format' => 'raw',
                'value' => function ($data) {
                    $clear_y = '<span class="text-danger"><strong>清库</strong></span>';
                    $clear_n = '<span class="text-success"><strong>在售</strong></span>';
                    return $data->clear == 1 ? $clear_y : $clear_n;
                },
            ],
            // 'arrival_days',
            [
                'class' => 'yii\grid\DataColumn', //由于是默认类型，可以省略 
                'header'=> '<a href="javascript:;">到货天数</a>',
                'value' => function ($data) {
                    if($data->arrival_days == 0){
                        static $common_days = 0;
                        if($common_days == 0){
                            $model_config = \backend\models\Config::findOne(['name'=>'GOODS_ARRIVAL_DAYS']);
                            if($model_config){
                                $common_days = $model_config->value;
                            }
                        }
                        $data->arrival_days = $common_days;
                    }
                    return $data->arrival_days; // 如果是数组数据则为 $data['name'] ，例如，使用 SqlDataProvider 的情形。
                },
            ],
            // 'status',
            // 'create_time:datetime',
            [
                'attribute' => 'create_time',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            [
                'attribute' => 'update_time',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],

            ['class' => 'yii\grid\ActionColumn','header'=> '<a href="javascript:;">操作</a>'],
        ],
    ]); ?>
</div>
<?php $this->beginBlock('footer');  ?>
    <script>
        $(function(){        
            $('#clear').val(<?= Yii::$app->request->get('c') ?>);

            $("#btn_query").on('click',function(){
                
                var url = location.href;
                var k = $.trim($('#keywords').val());
                var clear = $.trim($('#clear').val());
                
                if(url.indexOf("&k=") > 0){
                    url = changeUrlArg(url, "k", k);
                }else{
                    url += "&k=" + k;
                }

                if(url.indexOf("&c=") > 0){
                    url = changeUrlArg(url, "c", clear);
                }else{
                    url += "&c=" + clear;
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
<?php $this->endBlock(); ?>
