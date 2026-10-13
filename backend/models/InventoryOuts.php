<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Transaction;
/**
 * This is the model class for table "inventory_outs".
 *
 * @property int $id
 * @property int $project_id
 * @property string $transaction_date
 * @property int $status
 * @property int $inventory_period_id
 * @property string $created_at
 */
class InventoryOuts extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_outs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'transaction_date'], 'required'],
            [['transaction_date', 'created_at','status','inventory_period_id'], 'safe'],
            [['project_id','user_id',  'inventory_period_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'transaction_date' => 'Transaction Date',
            'status' => 'Status',
            'inventory_period_id' => 'Inventory Period ID',
            'user_id' => 'Created By',
            'created_at' => 'Created At',
        ];
    }
	
	public function beforeValidate()
	{
		if (!parent::beforeValidate())
		{
			return false;
		}

		if ($this->date_and_time)
		{
			$value = str_replace('T',' ',$this->date_and_time);

			if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/',$value))
			{
				$value .= ':00';
			}

			$this->date_and_time = $value;
		}

		return true;
	}
	
	public function getInventoryOut()
    {
        return $this->hasOne( InventoryOuts::class,['id' => 'inventory_out_id']);
    }

    public function getProduct()
    {
        return $this->hasOne( Products::class,['id' => 'product_id']);
    }

    public function getAllocations()
    {
        return $this->hasMany( InventoryOutItemAllocations::class,['inventory_out_item_id' => 'id']);
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
	public function getInventoryPeriod()
    {
        return $this->hasOne( InventoryPeriods::class,['id' => 'inventory_period_id']);
    }

    public function getProject()
    {
        return $this->hasOne( Projects::class,['id' => 'project_id']);
    }

    public function getUser()
    {
        return $this->hasOne( \common\models\User::class,['id' => 'user_id']);
    }

    public function getInventoryOutItems()
    {
        return $this->hasMany( InventoryOutItems::class,['inventory_out_id' => 'id']);
    }

    public function getInventoryOutChanges()
    {
        return $this->hasMany( InventoryOutChanges::class,['inventory_out_id' => 'id'])
            ->orderBy(['changed_at' => SORT_DESC,'id' => SORT_DESC]);
    }
	

	public static function getPeriodOptions(): array
	{
		$periods = InventoryPeriods::find()
			->orderBy([
				'year' => SORT_DESC,
				'month' => SORT_DESC,
			])
			->all();

		$options = [];

		foreach ($periods as $period)
		{
			$options[$period->id] = date('F Y',mktime(
				0,
				0,
				0,
				(int) $period->month,
				1,
				(int) $period->year
			));
		}

		return $options;
	}


    public function getTotalFifoCost(): string
    {
        $total = '0.00';

        foreach ($this->inventoryOutItems as $item)
        {
            $total = bcadd($total,$item->getFifoCost(),2);
        }

        return $total;
    }

    private function normalizeItems(array $rows): array
    {
        $items = [];

        if (empty($rows))
        {
            throw new \RuntimeException('Please add at least one product.');
        }

        foreach ($rows as $row)
        {
            $productId = filter_var($row['product_id'] ?? null,FILTER_VALIDATE_INT);
            $qty = filter_var($row['qty'] ?? null,FILTER_VALIDATE_INT);
            $remarks = trim((string) ($row['remarks'] ?? ''));

            if ($productId === false || $productId <= 0 || $qty === false || $qty <= 0)
            {
                throw new \RuntimeException('Each product must have a valid positive quantity.');
            }

            if (isset($items[$productId]))
            {
                throw new \RuntimeException('Duplicate products are not allowed.');
            }

            if (!Products::find()->where(['id' => $productId])->exists())
            {
                throw new \RuntimeException('One of the selected products does not exist.');
            }

            $items[$productId] = [
                'product_id' => $productId,
                'qty' => $qty,
                'remarks' => $remarks,
            ];
        }

        ksort($items,SORT_NUMERIC);

        return array_values($items);
    }

    private function recordChange(string $oldValue,string $newValue): void
    {
        if (strlen($oldValue) > 255 || strlen($newValue) > 255)
        {
            throw new \RuntimeException(
                'Change history exceeds 255 characters. The audit columns must be enlarged to store this transaction.'
            );
        }

        $change = new InventoryOutChanges();
        $change->inventory_out_id = $this->id;
        $change->old_value = $oldValue;
        $change->new_value = $newValue;
        $change->user_id = Yii::$app->user->id;
        $change->changed_at = date('Y-m-d H:i:s');

        if (!$change->save())
        {
            throw new \RuntimeException('Unable to record inventory change history.');
        }
    }

    public function saveWithItems(array $rows): bool
	{
		$db = static::getDb();
		$transaction = null;

		try
		{
			$items = $this->normalizeItems($rows);

			if (!$this->validate())
			{
				return false;
			}

			$isUpdate = !$this->isNewRecord;

			$transaction = $db->beginTransaction(
				\yii\db\Transaction::READ_COMMITTED
			);

			$oldHeader = [];
			$oldItems = [];

			// ==========================================
			// 1. LOCK EXISTING TRANSACTION ON UPDATE
			// ==========================================

			if ($isUpdate)
			{
				$oldHeader = $db->createCommand(
					'SELECT *
					 FROM inventory_outs
					 WHERE id = :id
					 FOR UPDATE',
					[':id' => $this->id]
				)->queryOne();

				if (!$oldHeader)
				{
					throw new \RuntimeException(
						'Inventory OUT transaction not found.'
					);
				}

				if ($oldHeader['status'] !== 'draft')
				{
					throw new \RuntimeException(
						'Only draft Inventory OUT transactions can be updated.'
					);
				}

				// Get existing products before replacing them.
				$oldItems = (new \yii\db\Query())
					->select(['product_id','qty','remarks'])
					->from('inventory_out_items')
					->where(['inventory_out_id' => $this->id])
					->all($db);

				// Prevent editing transactions in a closed period.
				$oldPeriod = InventoryPeriods::findOne(
					$oldHeader['inventory_period_id']
				);

				if (!$oldPeriod || $oldPeriod->status !== 'open')
				{
					throw new \RuntimeException(
						'The original inventory period is closed.'
					);
				}
			}

			// ==========================================
			// 2. VALIDATE INVENTORY PERIOD
			// ==========================================

			$period = InventoryPeriods::getOrCreatePeriod(
				$this->transaction_date
			);

			if ($period === null)
			{
				throw new \RuntimeException(
					'Unable to find or create the inventory period.'
				);
			}

			$periodRow = $db->createCommand(
				'SELECT status
				 FROM inventory_periods
				 WHERE id = :id
				 FOR UPDATE',
				[':id' => $period->id]
			)->queryOne();

			if (!$periodRow || $periodRow['status'] !== 'open')
			{
				throw new \RuntimeException(
					'The selected inventory period is closed.'
				);
			}

			$this->inventory_period_id = $period->id;

			// ==========================================
			// 3. LOCK PRODUCTS AND VALIDATE STOCK
			// ==========================================

			$productIds = array_column($items,'product_id');

			$productIds = array_values(
				array_unique(array_map('intval',$productIds))
			);

			sort($productIds,SORT_NUMERIC);

			foreach ($productIds as $productId)
			{
				$productRow = $db->createCommand(
					'SELECT id
					 FROM products
					 WHERE id = :id
					 FOR UPDATE',
					[':id' => $productId]
				)->queryOne();

				if (!$productRow)
				{
					throw new \RuntimeException(
						'Product ID ' . $productId . ' does not exist.'
					);
				}
			}

			// Uses Products::getAvailableQty().
			// Rejects zero stock or quantities exceeding availability.
			$this->validateAvailableStock($items);

			// ==========================================
			// 4. PREPARE INVENTORY OUT HEADER
			// ==========================================

			$this->status = 'draft';

			if (!$isUpdate)
			{
				$this->user_id = Yii::$app->user->id;
			}
			else
			{
				// Preserve original creator.
				$this->user_id = $oldHeader['user_id'];
			}

			// ==========================================
			// 5. SAVE HEADER
			// ==========================================

			if (!$this->save())
			{
				$errors = implode(' ',array_values(
					$this->getFirstErrors()
				));

				throw new \RuntimeException(
					$errors ?: 'Unable to save Inventory OUT header.'
				);
			}

			// ==========================================
			// 6. REPLACE ITEMS ON UPDATE
			// ==========================================

			if ($isUpdate)
			{
				// Only draft transactions reach this section.
				// Posted FIFO allocations are never deleted.
				InventoryOutItems::deleteAll([
					'inventory_out_id' => $this->id,
				]);
			}

			// ==========================================
			// 7. SAVE NEW PRODUCT ITEMS
			// ==========================================

			foreach ($items as $row)
			{
				$item = new InventoryOutItems();

				$item->inventory_out_id = $this->id;
				$item->product_id = $row['product_id'];
				$item->qty = $row['qty'];
				$item->remarks = $row['remarks'] ?? null;

				if (!$item->save())
				{
					$errors = implode(' ',array_values(
						$item->getFirstErrors()
					));

					throw new \RuntimeException(
						$errors ?: 'Unable to save Inventory OUT product.'
					);
				}
			}

			// ==========================================
			// 8. RECORD ACTUAL CHANGES
			// ==========================================

			if ($isUpdate)
			{
				// Project, transaction date, date and time.
				$this->recordHeaderChanges($oldHeader);

				// Added/removed products, changed Qty, changed remarks.
				$this->recordItemChanges($oldItems,$items);
			}

			// ==========================================
			// 9. COMMIT TRANSACTION
			// ==========================================

			$transaction->commit();

			$this->refresh();

			return true;
		}
		catch (\Throwable $e)
		{
			if ($transaction !== null && $transaction->isActive)
			{
				$transaction->rollBack();
			}

			Yii::error($e,__METHOD__);

			$this->addError('status',$e->getMessage());

			return false;
		}
	}

    public function postInventory(): bool
    {
        $db = static::getDb();
        $transaction = null;

        try
        {
            $transaction = $db->beginTransaction(Transaction::READ_COMMITTED);

            $header = $db->createCommand(
                'SELECT id,status,inventory_period_id
                 FROM inventory_outs WHERE id = :id FOR UPDATE',
                [':id' => $this->id]
            )->queryOne();

            if (!$header || $header['status'] !== 'draft')
            {
                throw new \RuntimeException('Only draft Inventory OUT transactions can be posted.');
            }

            $period = $db->createCommand(
                'SELECT status FROM inventory_periods WHERE id = :id FOR UPDATE',
                [':id' => $header['inventory_period_id']]
            )->queryOne();

            if (!$period || $period['status'] !== 'open')
            {
                throw new \RuntimeException('Inventory period is closed.');
            }

            $items = InventoryOutItems::find()
                ->where(['inventory_out_id' => $this->id])
                ->orderBy(['product_id' => SORT_ASC,'id' => SORT_ASC])
                ->all();

            if (empty($items))
            {
                throw new \RuntimeException('No products found to post.');
            }

            $productIds = array_values(array_unique(array_map(
                function($item)
                {
                    return (int) $item->product_id;
                },
                $items
            )));

            sort($productIds,SORT_NUMERIC);

            // All stock writers must follow this product-locking convention.
            foreach ($productIds as $productId)
            {
                $product = $db->createCommand(
                    'SELECT id FROM products WHERE id = :id FOR UPDATE',
                    [':id' => $productId]
                )->queryOne();

                if (!$product)
                {
                    throw new \RuntimeException('A selected product no longer exists.');
                }
            }

            foreach ($items as $item)
            {
                if ((int) $item->qty <= 0)
                {
                    throw new \RuntimeException('Invalid outgoing quantity.');
                }

                // Only posted incoming batches are available for FIFO.
                // Replace ii.qty with ii.quantity if needed.
                $batches = $db->createCommand(
                    'SELECT
                        ii.id,
                        ii.qty AS received_qty,
                        ii.amount,
                        COALESCE((
                            SELECT SUM(a.qty)
                            FROM inventory_out_item_allocations a
                            INNER JOIN inventory_out_items oi
                                ON oi.id = a.inventory_out_item_id
                            INNER JOIN inventory_outs o
                                ON o.id = oi.inventory_out_id
                            WHERE a.inventory_in_item_id = ii.id
                              AND o.status = :postedOut
                        ),0) AS allocated_qty
                     FROM inventory_in_items ii
                     INNER JOIN inventory_ins i
                        ON i.id = ii.inventory_in_id
                     WHERE ii.product_id = :productId
                       AND i.status = :postedIn
                     ORDER BY i.transaction_date ASC,i.id ASC,ii.id ASC',
                    [
                        ':postedOut' => 'posted',
                        ':postedIn' => 'posted',
                        ':productId' => $item->product_id,
                    ]
                )->queryAll();

                $remaining = (string) $item->qty;

                foreach ($batches as $batch)
                {
                    if (bccomp($remaining,'0',3) <= 0)
                    {
                        break;
                    }

                    $available = bcsub(
                        (string) $batch['received_qty'],
                        (string) $batch['allocated_qty'],
                        3
                    );

                    if (bccomp($available,'0',3) <= 0)
                    {
                        continue;
                    }

                    $take = bccomp($available,$remaining,3) < 0
                        ? $available
                        : $remaining;

                    $allocation = new InventoryOutItemAllocations();
                    $allocation->inventory_out_item_id = $item->id;
                    $allocation->inventory_in_item_id = $batch['id'];
                    $allocation->qty = $take;
                    $allocation->amount = $batch['amount'];
                    $allocation->total_amount = bcmul(
                        (string) $take,
                        (string) $batch['amount'],
                        2
                    );

                    if (!$allocation->save(false))
                    {
                        throw new \RuntimeException('Unable to save FIFO allocation.');
                    }

                    $remaining = bcsub($remaining,$take,3);
                }

                if (bccomp($remaining,'0',3) > 0)
                {
                    throw new \RuntimeException(
                        'Insufficient stock for product ID ' . $item->product_id .
                        '. Missing Qty: ' . $remaining
                    );
                }
            }

            $db->createCommand()->update(
                'inventory_outs',
                ['status' => 'posted'],
                ['id' => $this->id,'status' => 'draft']
            )->execute();

            $this->recordChange('draft','posted');

            $transaction->commit();
            $this->refresh();

            return true;
        }
        catch (\Throwable $e)
        {
            if ($transaction !== null && $transaction->isActive)
            {
                $transaction->rollBack();
            }

            Yii::error($e,__METHOD__);
            $this->addError('status',$e->getMessage());

            return false;
        }
    }
	
	private function validateAvailableStock(array $items): void
	{
		foreach ($items as $item)
		{
			$product = Products::findOne($item['product_id']);

			if ($product === null)
			{
				throw new \RuntimeException('Selected product does not exist.');
			}

			$availableQty = $product->getAvailableQty();
			$requestedQty = (int) $item['qty'];

			if ($availableQty <= 0)
			{
				throw new \RuntimeException(
					'Product "' . $product->name . '" has no available stock.'
				);
			}

			if (bccomp((string) $requestedQty,(string) $availableQty,3) > 0)
			{
				throw new \RuntimeException(
					'Insufficient stock for "' . $product->name . '". ' .
					'Available: ' . number_format($availableQty,3) .
					', Requested: ' . number_format($requestedQty)
				);
			}
		}
	}
	
	
	private function addChange(string $oldValue,string $newValue): void
	{
		if ($oldValue === $newValue)
		{
			return;
		}

		$change = new InventoryOutChanges();
		$change->inventory_out_id = $this->id;
		$change->old_value = $oldValue;
		$change->new_value = $newValue;
		$change->user_id = Yii::$app->user->id;
		$change->changed_at = date('Y-m-d H:i:s');

		if (!$change->save())
		{
			throw new \RuntimeException(
				'Unable to save Inventory OUT change history.'
			);
		}
	}

	private function recordHeaderChanges(array $oldHeader): void
	{
		$fields = [
			'project_id' => 'Project',
			'transaction_date' => 'Transaction Date',
			'date_and_time' => 'Date & Time',
		];

		foreach ($fields as $field => $label)
		{
			$old = (string) ($oldHeader[$field] ?? '');
			$new = (string) $this->$field;

			if ($old !== $new)
			{
				$this->addChange(
					$label . ': ' . $old,
					$label . ': ' . $new
				);
			}
		}
	}

	private function recordItemChanges(array $oldItems,array $newItems): void
	{
		$oldMap = [];
		$newMap = [];

		foreach ($oldItems as $item)
		{
			$oldMap[(int) $item['product_id']] = $item;
		}

		foreach ($newItems as $item)
		{
			$newMap[(int) $item['product_id']] = $item;
		}

		$productIds = array_unique(
			array_merge(array_keys($oldMap),array_keys($newMap))
		);

		foreach ($productIds as $productId)
		{
			$product = Products::findOne($productId);
			$productName = $product ? $product->name : 'Product #' . $productId;

			$oldItem = $oldMap[$productId] ?? null;
			$newItem = $newMap[$productId] ?? null;

			if ($oldItem === null)
			{
				$this->addChange(
					'Product: Not included',
					'Added: ' . $productName . ' (Qty: ' . $newItem['qty'] . ')'
				);

				continue;
			}

			if ($newItem === null)
			{
				$this->addChange(
					'Removed: ' . $productName . ' (Qty: ' . $oldItem['qty'] . ')',
					'Product: Not included'
				);

				continue;
			}

			if ((int) $oldItem['qty'] !== (int) $newItem['qty'])
			{
				$this->addChange(
					$productName . ' Qty: ' . $oldItem['qty'],
					$productName . ' Qty: ' . $newItem['qty']
				);
			}

			if ((string) ($oldItem['remarks'] ?? '') !== (string) ($newItem['remarks'] ?? ''))
			{
				$this->addChange(
					$productName . ' Remarks: ' . ($oldItem['remarks'] ?: '(empty)'),
					$productName . ' Remarks: ' . ($newItem['remarks'] ?: '(empty)')
				);
			}
		}
	}

}
