<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "config".
 *
 * @property integer $id
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property integer $group
 * @property string $name
 * @property string $value
 * @property string $extra
 * @property string $remark
 * @property integer $sort
 */
class Config extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'create_time', 'update_time', 'group', 'sort'], 'integer'],
            [['name', 'value', 'extra', 'remark'], 'string', 'max' => 255],
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
            'group' => '分组',
            'name' => '名称',
            'value' => '值',
            'extra' => '值扩展',
            'remark' => '备注',
            'sort' => '排序',
        ];
    }
}
