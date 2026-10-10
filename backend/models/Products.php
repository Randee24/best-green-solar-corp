<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $model
 * @property int|null $type_id
 * @property int|null $unit_id
 * @property float $amount
 * @property float $total_amount
 * @property float $installation_price
 * @property string $created_at
 */
class Products extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['model', 'product_type_id', 'unit_id'], 'default', 'value' => null],
            [['installation_price'], 'default', 'value' => 0.00],
            [['name', 'description'], 'required'],
            [['product_type_id', 'unit_id'], 'integer'],
            [['installation_price'], 'number'],
            [['created_at'], 'safe'],
            [['name', 'description'], 'string', 'max' => 255],
            [['model'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'model' => 'Model',
            'product_type_id' => 'Product Type',
            'unit_id' => 'Unit',
            'installation_price' => 'Installation Price',
            'created_at' => 'Created At',
        ];
    }
	
	
	public function getProductType()
    {
        return $this->hasOne(ProductTypes::class, ['id' => 'product_type_id']);
    }

 
    public function getUnit()
    {
        return $this->hasOne(Units::class, ['id' => 'unit_id']);
    }
	
	public function getProductPriceChanges()
	{
		return $this->hasMany( ProductPriceChanges::class,['product_id' => 'id']);
	}
	
	/**
	 * Get the current available quantity of this product.
	 *
	 * @return float
	 */
	public function getAvailableQty(): float
	{
		$db = static::getDb();

		$received = (float) (new \yii\db\Query())
			->from(['ii' => 'inventory_in_items'])
			->innerJoin(['i' => 'inventory_ins'],'i.id = ii.inventory_in_id')
			->where([
				'ii.product_id' => $this->id,
				'i.status' => 'posted',
			])
			->sum('ii.qty',$db);

		$issued = (float) (new \yii\db\Query())
			->from(['a' => 'inventory_out_item_allocations'])
			->innerJoin(['oi' => 'inventory_out_items'],'oi.id = a.inventory_out_item_id')
			->innerJoin(['o' => 'inventory_outs'],'o.id = oi.inventory_out_id')
			->where([
				'oi.product_id' => $this->id,
				'o.status' => 'posted',
			])
			->sum('a.qty',$db);

		return $received - $issued;
	}
	
	public static function getProductsWithStock(): array
	{
		$products = static::find()
			->orderBy(['name' => SORT_ASC])
			->all();

		$options = [];

		foreach ($products as $product)
		{
			$availableQty = $product->getAvailableQty();

			if ($availableQty > 0)
			{
				$options[$product->id] = $product->name .
					' (Available: ' . number_format($availableQty,3) . ')';
			}
		}

		return $options;
	}

}
