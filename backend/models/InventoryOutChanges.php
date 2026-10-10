<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "inventory_out_changes".
 *
 * @property int $id
 * @property int $inventory_out_id
 * @property string $old_value
 * @property string $new_value
 * @property int|null $user_id
 * @property string $changed_at
 * @property string $created_at
 */
class InventoryOutChanges extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_out_changes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'default', 'value' => null],
            [['inventory_out_id', 'old_value', 'new_value', 'changed_at'], 'required'],
            [['inventory_out_id', 'user_id'], 'integer'],
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
            'inventory_out_id' => 'Inventory Out ID',
            'old_value' => 'Old Value',
            'new_value' => 'New Value',
            'user_id' => 'User ID',
            'changed_at' => 'Changed At',
            'created_at' => 'Created At',
        ];
    }
	
	public function getInventoryOut()
    {
        return $this->hasOne( InventoryOuts::class,['id' => 'inventory_out_id']);
    }

    public function getUser()
    {
        return $this->hasOne( \common\models\User::class,['id' => 'user_id']);
    }

}
