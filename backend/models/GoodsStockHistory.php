<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods_stock_history".
 *
 * @property string $id
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $stock_date
 * @property string $code
 * @property string $stock
 */
class GoodsStockHistory extends \yii\db\ActiveRecord
{
    public $name;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_stock_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time', 'update_time', 'stock_date', 'stock'], 'integer'],
            [['code'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'create_time' => '创建时间',
            'update_time' => '修改时间',
            'stock_date' => '库存日期',
            'code' => '商家编码',
            'stock' => '库存数',
        ];
    }
}
