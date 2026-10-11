<?php

namespace backend\models;

use Yii;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
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
	public function getInventoryInChanges()
	{
		return $this->hasMany(InventoryInChanges::class,['inventory_in_id' => 'id'])
			->orderBy(['changed_at' => SORT_DESC,'id' => SORT_DESC]);
	}
	public function getUser()
	{
		return $this->hasOne(User::class,['id' => 'user_id']);
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
	
	public function postInventory($userId): bool
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
				 $supplierProduct = SupplierProducts::find()
										->where([
											'supplier_id' => $this->supplier_id,
											'product_id' => $item->product_id,
										])
										->one();
				
				 if ($supplierProduct === null)
					{
						$supplierProduct = new SupplierProducts();

						$supplierProduct->supplier_id = $this->supplier_id;
						$supplierProduct->product_id = $item->product_id;
						$supplierProduct->supplier_price = $item->amount;

						if (!$supplierProduct->save())
						{
							throw new \RuntimeException(
								'Unable to create supplier product: '
								. json_encode($supplierProduct->getErrors())
							);
						}

						// Initial price: no historical change yet.
						continue;
					}

					$supplierProduct->updatePriceFromInventoryIn(
						(float) $item->amount,
						$userId
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
	
	public function getTotalReceivedQty(): float
	{
		$total = 0;

		foreach ($this->inventoryInItems as $item)
		{
			$total += (float) $item->qty;
		}

		return $total;
	}

	public function getTotalReceivedAmount(): float
	{
		$total = 0;

		foreach ($this->inventoryInItems as $item)
		{
			$total += (float) $item->total_amount;
		}

		return $total;
	}

	public function getReceivedByDisplayName(): string
	{
		$user = $this->user;

		if ($user === null)
		{
			return 'N/A';
		}

		$name = trim(
			($user->first_name ?? '') . ' ' . ($user->last_name ?? '')
		);

		if ($name === '')
		{
			$name = $user->username;
		}

		$department = $user->department->name ?? '';

		return $name . ($department !== '' ? ' (' . $department . ')' : '');
	}
	
	public static function getProductOptions(): array
	{
		return ArrayHelper::map(
			Products::find()
				->orderBy(['name' => SORT_ASC])
				->asArray()
				->all(),
			'id',
			'name'
		);
	}
	
	public static function getSupplierOptions(): array
	{
		return ArrayHelper::map(
			Suppliers::find()
				->orderBy(['name' => SORT_ASC])
				->asArray()
				->all(),
			'id',
			'name'
		);
	}
	public function getFormItems(?array $postedItems = null): array
	{
		if ($postedItems !== null)
		{
			$items = array_values($postedItems);
		}
		elseif (!$this->isNewRecord)
		{
			$items = [];

			foreach ($this->inventoryInItems as $item)
			{
				$items[] = [
					'id' => $item->id,
					'product_id' => $item->product_id,
					'qty' => $item->qty,
					'amount' => $item->amount,
				];
			}
		}
		else
		{
			$items = [];
		}

		if (empty($items))
		{
			$items[] = [
				'product_id' => '',
				'qty' => 1,
				'amount' => '0.00',
			];
		}

		return $items;
	}
	
	private function logInventoryChange(string $oldValue, string $newValue, ?int $userId): void
	{
		$change = new InventoryInChanges();
		$change->inventory_in_id = $this->id;
		$change->old_value = mb_substr($oldValue,0,255);
		$change->new_value = mb_substr($newValue,0,255);
		$change->user_id = $userId;
		$change->changed_at = date('Y-m-d H:i:s');

		if (!$change->save())
		{
			throw new \RuntimeException(
				'Unable to save inventory change: ' .
				json_encode($change->getErrors())
			);
		}
	}
	public function updateWithItems(array $submittedItems, ?int $userId = null): bool
	{
		$db = static::getDb();
		$transaction = $db->beginTransaction();

		try
		{
			if ($this->isNewRecord || $this->status !== 'draft')
			{
				throw new \DomainException(
					'Only existing draft Inventory IN transactions can be edited.'
				);
			}

			$oldHeader = [
				'transaction_date' => $this->getOldAttribute('transaction_date'),
				'supplier_id' => $this->getOldAttribute('supplier_id'),
			];

			if (empty($submittedItems))
			{
				throw new \DomainException('At least one product is required.');
			}

			// Lock existing items while updating.
			$existingItems = InventoryInItems::find()
				->where(['inventory_in_id' => $this->id])
				->orderBy(['id' => SORT_ASC])
				->all();

			$existingById = [];

			foreach ($existingItems as $item)
			{
				$existingById[(int) $item->id] = $item;
			}

			$seenIds = [];
			$changes = [];

			// Validate the header before saving.
			if (!$this->validate())
			{
				return false;
			}

			foreach ($submittedItems as $row)
			{
				$itemId = (int) ($row['id'] ?? 0);
				$productId = (int) ($row['product_id'] ?? 0);
				$qty = (float) ($row['qty'] ?? 0);
				$amount = (float) ($row['amount'] ?? -1);

				if ($productId <= 0 || $qty <= 0 || $amount < 0)
				{
					throw new \DomainException(
						'Invalid product, quantity, or unit cost.'
					);
				}

				if ($itemId > 0)
				{
					if (!isset($existingById[$itemId]) || isset($seenIds[$itemId]))
					{
						throw new \DomainException(
							'Invalid or duplicated Inventory IN item ID.'
						);
					}

					$item = $existingById[$itemId];
					$seenIds[$itemId] = true;

					$oldProductId = (int) $item->product_id;
					$oldQty = (float) $item->qty;
					$oldAmount = (float) $item->amount;

					if ($oldProductId !== $productId)
					{
						$changes[] = [
							"Item #{$itemId} product: {$oldProductId}",
							"Item #{$itemId} product: {$productId}",
						];
					}

					if (abs($oldQty - $qty) > 0.000001)
					{
						$changes[] = [
							"Item #{$itemId} quantity: {$oldQty}",
							"Item #{$itemId} quantity: {$qty}",
						];
					}

					if (abs($oldAmount - $amount) > 0.000001)
					{
						$changes[] = [
							"Item #{$itemId} unit cost: {$oldAmount}",
							"Item #{$itemId} unit cost: {$amount}",
						];
					}
				}
				else
				{
					$item = new InventoryInItems();
					$item->inventory_in_id = $this->id;
				}

				$item->product_id = $productId;
				$item->qty = $qty;
				$item->amount = $amount;
				$item->total_amount = round($qty * $amount,2);

				if (!$item->save())
				{
					throw new \RuntimeException(
						'Unable to save receipt item: ' .
						json_encode($item->getErrors())
					);
				}

				if ($itemId === 0)
				{
					$changes[] = [
						'No item',
						"Added item #{$item->id}: Product #{$productId}, Qty {$qty}, Cost {$amount}",
					];
				}
			}

			// Remove existing items that were omitted from the form.
			foreach ($existingById as $itemId => $item)
			{
				if (isset($seenIds[$itemId]))
				{
					continue;
				}

				$changes[] = [
					"Removed item #{$itemId}: Product #{$item->product_id}, Qty {$item->qty}, Cost {$item->amount}",
					'Item removed',
				];

				if (!$item->delete())
				{
					throw new \RuntimeException(
						'Unable to remove Inventory IN item #' . $itemId
					);
				}
			}

			if (!$this->save(false))
			{
				throw new \RuntimeException('Unable to update Inventory IN.');
			}

			// Header change history.
			if ($oldHeader['transaction_date'] != $this->transaction_date)
			{
				$changes[] = [
					'Transaction date: ' . $oldHeader['transaction_date'],
					'Transaction date: ' . $this->transaction_date,
				];
			}

			if ((string) $oldHeader['supplier_id'] !== (string) $this->supplier_id)
			{
				$changes[] = [
					'Supplier ID: ' . $oldHeader['supplier_id'],
					'Supplier ID: ' . $this->supplier_id,
				];
			}

			foreach ($changes as [$oldValue, $newValue])
			{
				$this->logInventoryChange($oldValue,$newValue,$userId);
			}

			$transaction->commit();

			return true;
		}
		catch (\Throwable $e)
		{
			$transaction->rollBack();
			throw $e;
		}
	}
	public function getInventoryHistory(): array
	{
		$history = [];

		// Inventory creation event
		$history[] = [
			'type' => 'created',
			'title' => 'Inventory IN Created',
			'description' => 'Inventory receipt created',
			'date' => $this->created_at,
			'user_name' => $this->getReceivedByDisplayName(),
		];

		// Changes made to the Inventory IN
		$changes = InventoryInChanges::find()
			->alias('c')
			->select([
				'c.id',
				'c.old_value',
				'c.new_value',
				'c.changed_at',
				'u.username',
				'u.first_name',
				'u.last_name',
				'department_name' => 'd.name',
			])
			->leftJoin(['u' => 'user'],'u.id = c.user_id')
			->leftJoin(['d' => 'departments'],'d.id = u.department_id')
			->where(['c.inventory_in_id' => $this->id])
			->orderBy(['c.changed_at' => SORT_DESC,'c.id' => SORT_DESC])
			->asArray()
			->all();

		foreach ($changes as $change)
		{
			$name = trim(
				($change['first_name'] ?? '') . ' ' .
				($change['last_name'] ?? '')
			);

			if ($name === '')
			{
				$name = $change['username'] ?: 'Unknown User';
			}

			$department = $change['department_name'] ?? '';

			$isPosting = $change['old_value'] === 'Status: Draft' && $change['new_value'] === 'Status: Posted';

			$history[] = [
				'type' => $isPosting ? 'posted' : 'updated',
				'title' => $isPosting
					? 'Inventory IN Posted'
					: 'Inventory IN Updated',
				'description' => $isPosting
					? 'Inventory receipt successfully posted'
					: 'Receipt information modified',
				'old_value' => $change['old_value'],
				'new_value' => $change['new_value'],
				'date' => $change['changed_at'],
				'user_name' => $name,
				'department' => $department,
			];
		}

		// Sort newest first
		usort($history,function ($a,$b)
		{
			return strcmp(
				(string) $b['date'],
				(string) $a['date']
			);
		});

		return $history;
	}
	
	public function verifyPostingPin(string $pin, int $userId): bool
	{
		$user = User::findOne($userId);

		if ($user === null)
		{
			return false;
		}

		return $user->verifyPinCode($pin);
	}
	
	public function logPostingChange(int $userId): void
	{
		$change = new InventoryInChanges();

		$change->inventory_in_id = $this->id;
		$change->old_value = 'Status: Draft';
		$change->new_value = 'Status: Posted';
		$change->user_id = $userId;
		$change->changed_at = date('Y-m-d H:i:s');

		if (!$change->save())
		{
			throw new \RuntimeException(
				'Unable to record posting history: ' .
				json_encode($change->getErrors())
			);
		}
	}
}
