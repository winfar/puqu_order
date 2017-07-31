<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods_stock_record".
 *
 * @property string $id
 * @property string $create_time
 * @property string $goods_id
 * @property integer $update_type
 * @property integer $update_avlue
 * @property string $stock_before
 * @property string $stock_after
 */
class GoodsStockRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_stock_record';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time', 'goods_id', 'update_type', 'update_avlue', 'stock_before', 'stock_after'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'create_time' => '创建时间',
            'goods_id' => '商品id',
            'update_type' => '库存同步类型(1:全量,2:增量)',
            'update_avlue' => '更新量',
            'stock_before' => '更新前库存量',
            'stock_after' => '更新后库存量',
        ];
    }
}
