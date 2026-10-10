<?php

namespace backend\models;

use Yii;
use yii\db\Transaction;
/**
 * This is the model class for table "inventory_ins".
 *
 * @property int $id
 * @property int $supplier_id
 * @property string $transaction_date
 * @property int $status
 * @property int $inventory_period_id
 * @property string $created_at
 */
class InventoryIns extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_ins';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['supplier_id', 'transaction_date', 'status', 'inventory_period_id'], 'required'],
            [['transaction_date', 'created_at'], 'safe'],
            [['supplier_id','user_id',  'inventory_period_id'], 'integer'],
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
            'transaction_date' => 'Transaction Date',
            'status' => 'Status',
            'inventory_period_id' => 'Inventory Period ID',
            'user_id' => 'Created By',
            'created_at' => 'Created At',
        ];
    }
	
	public function getSupplier()
	{
		return $this->hasOne(Suppliers::class,['id' => 'supplier_id']);
	}

	public function getInventoryPeriod()
	{
		return $this->hasOne( InventoryPeriods::class,['id' => 'inventory_period_id']);
	}

	public function getInventoryInItems()
	{
		return $this->hasMany(InventoryInItems::class,['inventory_in_id' => 'id'])
			->orderBy(['id' => SORT_ASC]);
	}
	
	
	/**
	 * Saves an Inventory IN receipt with multiple products.
	 *
	 * @param array $rows Product rows submitted by the form.
	 * @return bool
	 */
	public function createWithItems(array $rows): bool
	{
		if (!$this->isNewRecord) {
			$this->addError('id', 'This receipt already exists.');
			return false;
		}

		$this->status = 'draft';
		$this->user_id = Yii::$app->user->id;
		$this->created_at = date('Y-m-d H:i:s');

		if (!$this->assignOpenPeriod()) {
			return false;
		}

		if (!$this->validate()) {
			return false;
		}

		$items = $this->prepareItems($rows);

		if ($items === false) {
			return false;
		}

		$db = static::getDb();

		$transaction = $db->beginTransaction(
			Transaction::READ_COMMITTED
		);

		try {
			// Lock the period to prevent closing during creation.
			$period = $db->createCommand(
				'SELECT id
				 FROM inventory_periods
				 WHERE id = :id AND status = :status
				 FOR UPDATE',
				[
					':id' => $this->inventory_period_id,
					':status' => 'open',
				]
			)->queryOne();

			if (!$period) {
				$this->addError(
					'transaction_date',
					'The inventory period is no longer open.'
				);

				$transaction->rollBack();
				return false;
			}

			// Save the receipt header.
			if (!$this->save(false)) {
				throw new \RuntimeException(
					'Failed to save inventory receipt.'
				);
			}

			// Save every product line.
			foreach ($items as $item) {

				$item->inventory_in_id = $this->id;

				if (!$item->save()) {
					throw new \RuntimeException(
						'Unable to save inventory item: ' .
						json_encode($item->getErrors())
					);
				}

				// Maintain supplier-specific price history.
				if (!SupplierProducts::recordPrice(
					(int) $this->supplier_id,
					(int) $item->product_id,
					(string) $item->amount
				)) {
					throw new \RuntimeException(
						'Unable to save supplier price history.'
					);
				}
			}
			$transaction->commit();
			return true;

		} catch (\Throwable $e)
		{
			if ($transaction->isActive)
			{
				$transaction->rollBack();
			}

			Yii::error([
				'message' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
				'trace' => $e->getTraceAsString(),
			],__METHOD__);

			if (YII_DEBUG)
			{
				$this->addError('id',$e->getMessage());
			}
			else
			{
				$this->addError('id','Unable to save inventory receipt. Please try again.');
			}

			return false;
		}
	}

	/**
	 * Find the open monthly period for the receipt date.
	 */
	private function assignOpenPeriod(): bool
	{
		$period = InventoryPeriods::getOrCreatePeriod(
			(string) $this->transaction_date
		);

		if ($period === null)
		{
			$this->addError(
				'transaction_date',
				'Unable to use this inventory period. It may already be closed.'
			);

			return false;
		}

		$this->inventory_period_id = $period->id;

		return true;
	}

	/**
	 * Validate and prepare product rows.
	 *
	 * @return InventoryInItems[]|false
	 */
	private function prepareItems(array $rows)
	{
		if (empty($rows)) {
			$this->addError(
				'id',
				'Please add at least one product.'
			);

			return false;
		}

		$items = [];
		$hasErrors = false;

		foreach (array_values($rows) as $index => $row) {

			if (!is_array($row)) {
				$this->addError(
					'id',
					'Invalid product data on row ' . ($index + 1)
				);
				$hasErrors = true;
				continue;
			}

			$item = new InventoryInItems();

			$item->product_id = $row['product_id'] ?? null;
			$item->qty = $row['qty'] ?? null;
			$item->amount = $row['amount'] ?? null;

			// Validate quantity and cost before calculating totals.
			$validQuantity = is_numeric($item->qty)
				&& (float) $item->qty > 0;

			$validAmount = is_numeric($item->amount)
				&& (float) $item->amount >= 0;

			if (!$validQuantity || !$validAmount) {
				$this->addError(
					'id',
					'Invalid quantity or unit cost on row ' . ($index + 1)
				);

				$hasErrors = true;
				continue;
			}

			// For financial amounts, calculate again on the server.
			$item->total_amount = number_format(
									round(
										(float) $item->qty * (float) $item->amount,2),2,'.',''
									);

			// Validate fields before inventory_in_id is assigned.
			if (!$item->validate(['product_id','qty','amount','total_amount',])) 
			{
				$this->addError('id','Product row ' . ($index + 1) . ': ' .json_encode($item->getErrors())
					);

				$hasErrors = true;
			}

			$items[] = $item;
		}

		return $hasErrors ? false : $items;
	}
	
	public function postInventory(): bool
	{
		$db = static::getDb();

		if ($this->isNewRecord) {
			$this->addError('status', 'Save the receipt before posting.');
			return false;
		}

		$transaction = $db->beginTransaction(Transaction::READ_COMMITTED);

		try {
			// Lock receipt to prevent simultaneous posting.
			$receipt = $db->createCommand(
				'SELECT id, status, inventory_period_id, supplier_id
				 FROM inventory_ins
				 WHERE id = :id
				 FOR UPDATE',
				[':id' => $this->id]
			)->queryOne();

			if (!$receipt) {
				throw new \RuntimeException('Inventory receipt not found.');
			}

			if ($receipt['status'] !== 'draft') {
				throw new \RuntimeException(
					'Only draft inventory receipts can be posted.'
				);
			}

			if (empty($receipt['supplier_id'])) {
				throw new \RuntimeException(
					'Please select a supplier before posting.'
				);
			}

			// Lock inventory period.
			$period = $db->createCommand(
				'SELECT id, status
				 FROM inventory_periods
				 WHERE id = :id
				 FOR UPDATE',
				[':id' => $receipt['inventory_period_id']]
			)->queryOne();

			if (!$period || $period['status'] !== 'open') {
				throw new \RuntimeException(
					'Cannot post inventory into a closed period.'
				);
			}

			// Load fresh items from the database.
			$items = InventoryInItems::find()
				->where(['inventory_in_id' => $this->id])
				->orderBy(['id' => SORT_ASC])
				->all();

			if (empty($items)) {
				throw new \RuntimeException(
					'Cannot post an inventory receipt without products.'
				);
			}

			foreach ($items as $item) {

				if (!Products::find()->where(['id' => $item->product_id])->exists()) {
					throw new \RuntimeException(
						"Product ID {$item->product_id} does not exist."
					);
				}

				if (!is_numeric($item->qty) ||
					(float) $item->qty <= 0 ||
					!is_numeric($item->amount) ||
					(float) $item->amount < 0) 
				{
					throw new \RuntimeException(
						"Invalid quantity or amount for product {$item->product_id}."
					);
				}

				// Check that total_amount matches quantity * amount.
				$expectedTotal = bcmul((string) $item->qty,(string) $item->amount,2
					);

				if (bccomp($expectedTotal,(string) $item->total_amount,2) !== 0) 
				{
					throw new \RuntimeException(
						"Incorrect total amount for product {$item->product_id}."
					);
				}

				// Find or create supplier price history.
				$supplierProduct = SupplierProducts::findOrCreatePrice(
					(int) $receipt['supplier_id'],
					(int) $item->product_id,
					(string) $item->amount
				);

				// Link item to exact supplier price version.
				$item->supplier_product_id = $supplierProduct->id;

				if (!$item->save(false, ['supplier_product_id'])) {
					throw new \RuntimeException(
						"Unable to link supplier price for product {$item->product_id}."
					);
				}
			}

			// Change receipt status to posted.
			$affectedRows = $db->createCommand()->update(
				'inventory_ins',
				['status' => 'posted'],
				[
					'id' => $this->id,
					'status' => 'draft',
				]
			)->execute();

			if ($affectedRows !== 1) {
				throw new \RuntimeException(
					'Receipt status changed before posting could complete.'
				);
			}

			$transaction->commit();

			// Keep the ActiveRecord instance in sync.
			$this->setOldAttribute('status', 'posted');
			$this->status = 'posted';

			return true;

		} catch (\Throwable $e) {

			if ($transaction->isActive) {
				$transaction->rollBack();
			}

			Yii::error($e, __METHOD__);

			$this->addError('status', $e->getMessage());

			return false;
		}
	}

}
