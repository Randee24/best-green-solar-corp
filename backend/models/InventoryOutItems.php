<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "inventory_out_items".
 *
 * @property int $id
 * @property int $inventory_out_id
 * @property int $product_id
 * @property float $amount
 * @property int $qty
 * @property string|null $remarks
 * @property string $created_at
 */
class InventoryOutItems extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_out_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['remarks'], 'default', 'value' => null],
            // [['amount','total_amount'], 'default', 'value' => 0.00],
            [['qty'], 'default', 'value' => 0],
            [['inventory_out_id', 'product_id'], 'required'],
            [['inventory_out_id', 'product_id', 'qty'], 'integer'],
            // [['amount','total_amount'], 'number'],
            [['remarks'], 'string'],
            [['created_at'], 'safe'],
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
            'product_id' => 'Product ID',
            //'amount' => 'Amount',
            //'total_amount' => 'Total Amount',
            'qty' => 'Qty',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
        ];
    }
	
	
	public function getInventoryOut()
	{
		return $this->hasOne(InventoryOuts::class,['id' => 'inventory_out_id']);
	}

	public function getProduct()
	{
		return $this->hasOne(Products::class,['id' => 'product_id']);
	}

	public function getAllocations()
	{
		return $this->hasMany(InventoryOutItemAllocations::class,['inventory_out_item_id' => 'id']);
	}
	public function getFifoCost(): string
    {
        $total = '0.00';

        foreach ($this->allocations as $allocation)
        {
            $total = bcadd($total,(string) $allocation->total_amount,2);
        }

        return $total;
    }

}
