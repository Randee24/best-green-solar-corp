<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "inventory_period_product_balances".
 *
 * @property int $id
 * @property int $inventory_period_id
 * @property int $product_id
 * @property float $opening_qty
 * @property float $in_qty
 * @property float $out_qty
 * @property float $closing_qty
 * @property float $opening_value
 * @property float $in_value
 * @property float $out_value
 * @property float $closing_value
 * @property string $created_at
 * @property string|null $updated_at
 *
 * @property InventoryPeriods $inventoryPeriod
 * @property Products $product
 */
class InventoryPeriodProductBalances extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_period_product_balances';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['updated_at'], 'default', 'value' => null],
            [['closing_qty'], 'default', 'value' => 0.000],
            [['closing_value'], 'default', 'value' => 0.00],
            [['inventory_period_id', 'product_id'], 'required'],
            [['inventory_period_id', 'product_id'], 'integer'],
            [['opening_qty', 'in_qty', 'out_qty', 'closing_qty', 'opening_value', 'in_value', 'out_value', 'closing_value'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['inventory_period_id', 'product_id'], 'unique', 'targetAttribute' => ['inventory_period_id', 'product_id']],
            [['inventory_period_id'], 'exist', 'skipOnError' => true, 'targetClass' => InventoryPeriods::class, 'targetAttribute' => ['inventory_period_id' => 'id']],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['product_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inventory_period_id' => 'Inventory Period ID',
            'product_id' => 'Product ID',
            'opening_qty' => 'Opening Qty',
            'in_qty' => 'In Qty',
            'out_qty' => 'Out Qty',
            'closing_qty' => 'Closing Qty',
            'opening_value' => 'Opening Value',
            'in_value' => 'In Value',
            'out_value' => 'Out Value',
            'closing_value' => 'Closing Value',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[InventoryPeriod]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInventoryPeriod()
    {
        return $this->hasOne(InventoryPeriods::class, ['id' => 'inventory_period_id']);
    }

    /**
     * Gets query for [[Product]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Products::class, ['id' => 'product_id']);
    }

}
