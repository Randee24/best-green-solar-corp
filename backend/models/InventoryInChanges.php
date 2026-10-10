<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "inventory_in_changes".
 *
 * @property int $id
 * @property int $inventory_in_id
 * @property string $old_value
 * @property string $new_value
 * @property int|null $user_id
 * @property string $changed_at
 * @property string $created_at
 */
class InventoryInChanges extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_in_changes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'default', 'value' => null],
            [['inventory_in_id', 'old_value', 'new_value', 'changed_at'], 'required'],
            [['inventory_in_id', 'user_id'], 'integer'],
            [['changed_at', 'created_at'], 'safe'],
            [['old_value', 'new_value'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inventory_in_id' => 'Inventory In ID',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
            'user_id' => 'User ID',
            'changed_at' => 'Changed At',
            'created_at' => 'Created At',
        ];
    }

}
