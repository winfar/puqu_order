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

    <div style="margin-top:20px;">
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'id',
            'code',
            'name',
            'stock',
            [
                'class' => 'yii\grid\DataColumn', //由于是默认类型，可以省略 
                'header'=> '<a href="javascript:;">到货天数</a>',
                'value' => function ($data) {
                    static $common_days = 0;
                    if($data->arrival_days == 0){
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
            [
                'header'=> '<a href="javascript:;">出货量</a>',
                'value' => function ($data) {
                    return $data->out_qty; // 如果是数组数据则为 $data['name'] ，例如，使用 SqlDataProvider 的情形。
                },
            ]
        ],
    ]); ?>
</div>
<script>
    $(function(){        
        $("#btn_query").on('click',function(){
            
            var url = location.href;
            var k = $.trim($('#keywords').val());
            
            if(url.indexOf("&k=") > 0){
                url = changeUrlArg(url, "k", k);
            }else{
                url += "&k=" + k;
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
