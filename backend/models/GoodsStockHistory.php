<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods_stock_history".
 *
 * @property string $id
 * @property string $create_date
 * @property string $goods_id_stocks
 */
class GoodsStockHistory extends \yii\db\ActiveRecord
{
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
            [['create_date'], 'safe'],
            [['goods_id_stocks'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_date' => '创建时间',
            'goods_id_stocks' => '商品库存集合',
        ];
    }
}
