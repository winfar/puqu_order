<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Goods';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="goods-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <p style="margin-top:20px;">
        <?= Html::a('Create Goods', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('导入商品', ['import'], ['class' => 'btn btn-success']) ?>
        <!--<a href="import" class="btn btn-success">导入商品</a>-->
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn'],
            'id',
            'code',
            'name',
            // 'barcode',
            // 'category_id',
            'category_name',
            'brand',
            'supplier',
            'specification',
            'price',
            'stock',
            'stock_position',
            'clear',
            // 'arrival_days',
            [
                'class' => 'yii\grid\DataColumn', //由于是默认类型，可以省略 
                'header'=> '到货天数',
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
            'status',
            // 'create_time:datetime',
            [
                'attribute' => 'create_time',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
