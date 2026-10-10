<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "product_price_changes".
 *
 * @property int $id
 * @property int $product_id
 * @property string $old_price
 * @property string $new_price
 * @property int|null $user_id
 * @property string $changed_at
 * @property string $created_at
 */
class ProductPriceChanges extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_price_changes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'default', 'value' => null],
            [['product_id','supplier_product_id', 'old_price', 'new_price', 'changed_at'], 'required'],
            [['product_id','supplier_product_id', 'user_id'], 'integer'],
            [['changed_at', 'created_at'], 'safe'],
            [['old_price', 'new_price'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'supplier_product_id' => 'Supplier ID',
            'product_id' => 'Product ID',
            'old_price' => 'Old Price',
            'new_price' => 'New Price',
            'user_id' => 'User ID',
            'changed_at' => 'Changed At',
            'created_at' => 'Created At',
        ];
    }
	
    public function getProduct()
    {
        return $this->hasOne( Products::class,['id' => 'product_id']);
    }

    public function getSupplierProduct()
    {
        return $this->hasOne( SupplierProducts::class,['id' => 'supplier_product_id']);
    }

    public function getUser()
    {
        return $this->hasOne( User::class,['id' => 'user_id']);
    }

}
