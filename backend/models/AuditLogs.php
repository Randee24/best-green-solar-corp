<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "audit_logs".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $table_name
 * @property int $record_id
 * @property string $action
 * @property string|null $old_values
 * @property string|null $new_values
 * @property string $created_at
 */
class AuditLogs extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'audit_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'old_values', 'new_values'], 'default', 'value' => null],
            [['user_id', 'record_id'], 'integer'],
            [['table_name', 'record_id', 'action'], 'required'],
            [['old_values', 'new_values'], 'string'],
            [['created_at'], 'safe'],
            [['table_name'], 'string', 'max' => 255],
            [['action'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'table_name' => 'Table Name',
            'record_id' => 'Record ID',
            'action' => 'Action',
            'old_values' => 'Old Values',
            'new_values' => 'New Values',
            'created_at' => 'Created At',
        ];
    }

}
