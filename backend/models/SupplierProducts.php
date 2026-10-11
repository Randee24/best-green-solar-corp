<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "supplier_products".
 *
 * @property int $id
 * @property int $supplier_id
 * @property int $product_id
 * @property float $supplier_price
 * @property string|null $supplier_product_code
 * @property string $created_at
 */
class SupplierProducts extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'supplier_products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supplier_product_code'], 'default', 'value' => null],
            [['supplier_price'], 'default', 'value' => 0.00],
            [['supplier_id', 'product_id'], 'required'],
            [['supplier_id', 'product_id'], 'integer'],
            [['supplier_price'], 'number'],
            [['created_at'], 'safe'],
			[
				['supplier_id','product_id'],
				'unique',
				'targetAttribute' => ['supplier_id','product_id'],
				'message' => 'This supplier already has this product.',
			],
            [['supplier_product_code'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'supplier_id' => 'Supplier ID',
            'product_id' => 'Product ID',
            'supplier_price' => 'Supplier Price',
            'supplier_product_code' => 'Supplier Product Code',
            'created_at' => 'Created At',
        ];
    }
	
	public function getSupplier()
	{
		return $this->hasOne( Suppliers::class,['id' => 'supplier_id']);
	}

	public function getProduct()
	{
		return $this->hasOne( Products::class,['id' => 'product_id']);
	}

	public function getPriceChanges()
	{
		return $this->hasMany( ProductPriceChanges::class,['supplier_product_id' => 'id']);
	}
	
	public function getInventoryInItems()
	{
		return $this->hasMany(InventoryInItems::class,['supplier_product_id' => 'id']);
	}
	
	
	public static function recordPrice(int $supplierId,int $productId,string $price,?string $productCode = null): bool 
	{
		$latest = self::find()->where([
            'supplier_id' => $supplierId,
            'product_id' => $productId,
        ])->orderBy([
            'created_at' => SORT_DESC,
            'id' => SORT_DESC,
        ])->one();

		if ($latest !== null) {
			return true;
		}

		$record = new self();
		$record->supplier_id = $supplierId;
		$record->product_id = $productId;
		$record->supplier_price = $price;
		$record->supplier_product_code = $productCode ?? ($latest->supplier_product_code ?? null);

		return $record->save();
	}
	
	public static function findOrCreatePrice(int $supplierId,int $productId,string $price): self
	{
		$price = number_format((float) $price, 2, '.', '');

		$latest = self::find()->where([
				'supplier_id' => $supplierId,
				'product_id' => $productId,
			])->orderBy([
				'created_at' => SORT_DESC,
				'id' => SORT_DESC,
			])->one();

		// Reuse the latest record if the price is unchanged.
		if ($latest !== null && bccomp((string) $latest->supplier_price, $price, 2) === 0) 
		{
			return $latest;
		}

		$record = new self();
		$record->supplier_id = $supplierId;
		$record->product_id = $productId;
		$record->supplier_price = $price;
		$record->supplier_product_code = $latest->supplier_product_code ?? null;

		if (!$record->save()) {
			throw new \RuntimeException(
				'Unable to save supplier price: ' .
				json_encode($record->getErrors())
			);
		}
		
		if ($latest !== null)
		{
			$history = new ProductPriceChanges();

			$history->product_id = $productId;
			$history->supplier_product_id = $record->id;
			$history->old_price = (string) $latest->supplier_price;
			$history->new_price = (string) $record->supplier_price;
			$history->user_id = Yii::$app->user->isGuest ? null : Yii::$app->user->id;
			$history->changed_at = date('Y-m-d H:i:s');

			if (!$history->save())
			{
				throw new \RuntimeException(
					'Unable to record product price change: ' .
					json_encode($history->getErrors())
				);
			}
		}

		return $record;
	}
	
	public function updatePriceFromInventoryIn(float $newPrice,int $userId): bool
	{
		$oldPrice = round((float) $this->supplier_price,2);
		$newPrice = round($newPrice,2);

		// No change in price, no history needed.
		if ($oldPrice === $newPrice)
		{
			return true;
		}

		$change = new ProductPriceChanges();

		$change->supplier_product_id = $this->id;
		$change->product_id = $this->product_id;

		// Store DECIMAL values as formatted strings.
		$change->old_price = number_format($oldPrice,2,'.','');
		$change->new_price = number_format($newPrice,2,'.','');

		$change->user_id = $userId;
		$change->changed_at = date('Y-m-d H:i:s');

		if (!$change->validate())
		{
			throw new \RuntimeException(
				'Unable to validate supplier price change: '
				. json_encode($change->getErrors())
			);
		}

		$this->supplier_price = number_format($newPrice,2,'.','');

		if (!$this->save(false,['supplier_price']))
		{
			throw new \RuntimeException(
				'Unable to update supplier price.'
			);
		}

		if (!$change->save(false))
		{
			throw new \RuntimeException(
				'Unable to record supplier price change.'
			);
		}

		return true;
	}

}
