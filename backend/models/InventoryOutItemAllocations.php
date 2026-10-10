<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "inventory_out_item_allocations".
 *
 * @property int $id
 * @property int $inventory_out_item_id
 * @property int $inventory_in_item_id
 * @property float $qty
 * @property float $amount
 * @property float $total_amount
 * @property string $created_at
 *
 * @property InventoryInItems $inventoryInItem
 * @property InventoryOutItems $inventoryOutItem
 */
class InventoryOutItemAllocations extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_out_item_allocations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty'], 'default', 'value' => 0.000],
            [['total_amount'], 'default', 'value' => 0.00],
            [['inventory_out_item_id', 'inventory_in_item_id'], 'required'],
            [['inventory_out_item_id', 'inventory_in_item_id'], 'integer'],
            [['qty', 'amount', 'total_amount'], 'number'],
            [['created_at'], 'safe'],
            [['inventory_in_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryInItems::class, 'targetAttribute' => ['inventory_in_item_id' => 'id']],
            [['inventory_out_item_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryOutItems::class, 'targetAttribute' => ['inventory_out_item_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inventory_out_item_id' => 'Inventory Out Item ID',
            'inventory_in_item_id' => 'Inventory In Item ID',
            'qty' => 'Qty',
            'amount' => 'Amount',
            'total_amount' => 'Total Amount',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[InventoryInItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryInItem()
    {
        return $this->hasOne(InventoryInItems::class, ['id' => 'inventory_in_item_id']);
    }

    /**
     * Gets query for [[InventoryOutItem]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryOutItem()
    {
        return $this->hasOne(InventoryOutItems::class, ['id' => 'inventory_out_item_id']);
    }

}
