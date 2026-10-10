<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "inventory_in_items".
 *
 * @property int $id
 * @property int $inventory_in_id
 * @property int $product_id
 * @property float $amount
 * @property int $qty
 * @property string|null $remarks
 * @property string $created_at
 */
class InventoryInItems extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_in_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['remarks'], 'default', 'value' => null],
            [['amount','total_amount'], 'default', 'value' => 0.00],
            [['qty','remaining_qty'], 'default', 'value' => 0],
            [['inventory_in_id', 'product_id'], 'required'],
            [['inventory_in_id', 'product_id', 'qty','remaining_qty'], 'integer'],
            [['amount','total_amount'], 'number'],
            [['remarks'], 'string'],
            [['created_at'], 'safe'],
			[['supplier_product_id'], 'integer'],
			[
				['supplier_product_id'],
				'exist',
				'skipOnError' => true,
				'targetClass' => SupplierProducts::class,
				'targetAttribute' => ['supplier_product_id' => 'id'],
			],
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
            'product_id' => 'Product ID',
            'amount' => 'Amount',
            'total_amount' => 'Total Amount',
            'qty' => 'Qty',
            'remaining_qty' => 'Remaining Qty',
            'remarks' => 'Remarks',
            'created_at' => 'Created At',
        ];
    }
	
	public function getProduct()
	{
		return $this->hasOne(Products::class,['id' => 'product_id']);
	}

	public function getSupplierProduct()
	{
		return $this->hasOne(SupplierProducts::class,['id' => 'supplier_product_id']);
	}

}
