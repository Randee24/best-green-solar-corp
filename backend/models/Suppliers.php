<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "suppliers".
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 */
class Suppliers extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'suppliers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'created_at' => 'Created At',
        ];
    }
	
	public function getSupplierProducts()
	{
		return $this->hasMany( SupplierProducts::class,['supplier_id' => 'id']);
	}
	
	/**
	 * Get the latest supplier price record for each product.
	 */
	public function getLatestSupplierProducts()
	{
		$latestIds = (new \yii\db\Query())
			->select('MAX(sp2.id)')
			->from(['sp2' => 'supplier_products'])
			->where(['sp2.supplier_id' => $this->id])
			->groupBy('sp2.product_id');

		return SupplierProducts::find()
			->alias('sp')
			->where(['sp.id' => $latestIds])
			->with(['product'])
			->orderBy(['sp.id' => SORT_DESC]);
	}

}
