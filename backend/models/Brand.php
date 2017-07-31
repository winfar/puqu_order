<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "brand".
 *
 * @property integer $id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property string $name
 * @property integer $arrival_days
 */
class Brand extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'brand';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'create_time', 'update_time', 'arrival_days'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'name' => '品牌名称',
            'arrival_days' => '到货天数',
        ];
    }
}
