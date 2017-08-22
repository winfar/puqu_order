<?php

namespace backend\controllers;

use Yii;
use yii\data\SqlDataProvider;
use backend\models\GoodsStockHistory;

class GoodsStockHistoryController extends \backend\controllers\BaseController
{
    public $layout = "lte_main";

    public function actionIndex()
    {
        $date = Yii::$app->request->get('d');

        if(empty($date)){
            $date = date('Ymd');
        }

        $timestamp = strtotime($date);

        // echo $timestamp;exit;

        $condition='';

        $sql = 'select g.`name`,gsh.`code`,gsh.stock,gsh.stock_date,gsh.create_time ,gsh.update_time 
                from goods g, goods_stock_history gsh 
                WHERE g.`code`=gsh.`code`
                AND stock_date = '.$timestamp.'
                ORDER BY gsh.stock_date DESC,`code`';

        $rows = GoodsStockHistory::findBySql($sql)->all();
        $totalCount = count($rows);

        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'totalCount' => $totalCount,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

}
