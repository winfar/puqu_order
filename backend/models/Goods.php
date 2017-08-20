<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods".
 *
 * @property string $id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $name
 * @property string $code
 * @property string $barcode
 * @property integer $category_id
 * @property string $category_name
 * @property string $brand
 * @property string $supplier
 * @property string $specification
 * @property string $price
 * @property string $stock
 * @property string $stock_position
 * @property integer $clear
 * @property integer $arrival_days
 */
class Goods extends \yii\db\ActiveRecord
{
    public $out_qty;
    public $is_stock_in;
    public $order_qty;
    public $out_qty_average;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'create_time', 'update_time', 'category_id', 'stock', 'clear', 'arrival_days'], 'integer'],
            [['price'], 'number'],
            [['name', 'specification'], 'string', 'max' => 128],
            [['code', 'barcode', 'stock_position'], 'string', 'max' => 32],
            [['category_name', 'brand', 'supplier'], 'string', 'max' => 64],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => '状态(0:禁用,1:启用)',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'name' => '商品名称',
            'code' => '商家编码',
            'barcode' => '条码',
            'category_id' => 'Category ID',
            'category_name' => '品牌分类',
            'brand' => '品牌',
            'supplier' => '供货商',
            'specification' => '规格名称',
            'price' => '单价',
            'stock' => '实际库存数',
            'stock_position' => '库位',
            'clear' => '是否清库(1:是,0:否)',
            'arrival_days' => '到货天数',
            'out_qty' => '出货量',
            'out_qty_average' =>'日均销量',
        ];
    }
}
