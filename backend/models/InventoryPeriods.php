<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "inventory_periods".
 *
 * @property int $id
 * @property int $year
 * @property int $month
 * @property string $status
 * @property int|null $closed_by
 * @property string|null $closed_at
 */
class InventoryPeriods extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_periods';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['closed_by'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'open'],
            [['year', 'month'], 'required'],
            [['year', 'month', 'closed_by'], 'integer'],
            [['status'], 'string'],
            [['closed_at'], 'safe'],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['year', 'month'], 'unique', 'targetAttribute' => ['year', 'month']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'year' => 'Year',
            'month' => 'Month',
            'status' => 'Status',
            'closed_by' => 'Closed By',
            'closed_at' => 'Closed At',
        ];
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_OPEN => 'open',
            self::STATUS_CLOSED => 'closed',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusOpen()
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function setStatusToOpen()
    {
        $this->status = self::STATUS_OPEN;
    }

    /**
     * @return bool
     */
    public function isStatusClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function setStatusToClosed()
    {
        $this->status = self::STATUS_CLOSED;
    }
	public function getInventoryIns()
	{
		return $this->hasMany( InventoryIns::class,['inventory_period_id' => 'id']);
	}

	public function getPeriodName(): string
	{
		return date('F Y',mktime(0,0,0,(int) $this->month,1,(int) $this->year));
	}

	public function getIsOpen(): bool
	{
		return $this->status === 'open';
	}

	public function getInventoryInCount(): int
	{
		return (int) $this->getInventoryIns()->count();
	}

	public function getPostedInventoryInCount(): int
	{
		return (int) $this->getInventoryIns()
			->andWhere(['status' => 'posted'])
			->count();
	}
	
	public function getInventoryOuts()
	{
		return $this->hasMany( InventoryOuts::class,['inventory_period_id' => 'id']);
	}

	public function getInventoryInTotal(): string
	{
		$total = (new \yii\db\Query())
			->from(['ii' => 'inventory_in_items'])
			->innerJoin(['i' => 'inventory_ins'],'i.id = ii.inventory_in_id')
			->where([
				'i.inventory_period_id' => $this->id,
				'i.status' => 'posted',
			])
			->sum('ii.total_amount');

		return (string) ($total ?? '0.00');
	}
	
	/**
	 * Get or create the inventory period for the given date.
	 * Multiple months can remain open simultaneously.
	 */
	public static function getOrCreatePeriod(string $transactionDate): ?self
	{
		$date = \DateTimeImmutable::createFromFormat('!Y-m-d',$transactionDate);

		if (!$date || $date->format('Y-m-d') !== $transactionDate)
		{
			return null;
		}

		$year = (int) $date->format('Y');
		$month = (int) $date->format('n');

		// Find the exact month and year, regardless of other open periods.
		$period = self::findOne([
			'year' => $year,
			'month' => $month,
		]);

		if ($period !== null)
		{
			return $period->status === 'open' ? $period : null;
		}

		// No period exists for this month, so create it.
		$period = new self();
		$period->year = $year;
		$period->month = $month;
		$period->status = 'open';

		if (!$period->save())
		{
			// Handle simultaneous requests creating the same period.
			$existing = self::findOne([
				'year' => $year,
				'month' => $month,
			]);

			if ($existing !== null && $existing->status === 'open')
			{
				return $existing;
			}

			\Yii::error($period->getErrors(),__METHOD__);
			return null;
		}

		return $period;
	}
	
	
	public static function createCurrentMonth(): array
	{
		$year = (int) date('Y');
		$month = (int) date('n');

		$db = static::getDb();
		$transaction = $db->beginTransaction();

		try
		{
			$existing = static::find()
				->where([
					'year' => $year,
					'month' => $month,
				])
				->one();

			if ($existing !== null)
			{
				$transaction->commit();

				return [
					'success' => false,
					'message' => 'The inventory period for this month already exists.',
					'id' => $existing->id,
				];
			}

			$model = new static();
			$model->year = $year;
			$model->month = $month;
			$model->status = 'open';

			if (!$model->save())
			{
				throw new \RuntimeException(
					implode(' ',array_values($model->getFirstErrors()))
				);
			}

			$transaction->commit();

			return [
				'success' => true,
				'message' => 'Inventory period for ' .
					date('F Y') . ' created successfully.',
				'id' => $model->id,
			];
		}
		catch (\yii\db\IntegrityException $e)
		{
			$transaction->rollBack();

			return [
				'success' => false,
				'message' => 'The inventory period already exists or could not be created.',
				'id' => null,
			];
		}
		catch (\Throwable $e)
		{
			$transaction->rollBack();
			Yii::error($e,__METHOD__);

			return [
				'success' => false,
				'message' => 'Unable to create the inventory period.',
				'id' => null,
			];
		}
	}
	
	public static function ensureCurrentMonth(): self
	{
		$year = (int) date('Y');
		$month = (int) date('n');

		$period = static::findOne([
			'year' => $year,
			'month' => $month,
		]);

		if ($period !== null)
		{
			return $period;
		}

		$period = new static();
		$period->year = $year;
		$period->month = $month;
		$period->status = 'open';

		if (!$period->save())
		{
			// Another request may have created it concurrently.
			$existing = static::findOne([
				'year' => $year,
				'month' => $month,
			]);

			if ($existing !== null)
			{
				return $existing;
			}

			throw new \RuntimeException(
				'Unable to create the current inventory period: ' .
				implode(' ',array_values($period->getFirstErrors()))
			);
		}

		return $period;
	}
	
	
	public function closePeriod(int $userId,string $pin): bool
	{
		$db = static::getDb();
		$transaction = null;

		try
		{
			$user = User::findOne($userId);

			if ($user === null || !$user->verifyPinCode($pin))
			{
				throw new \RuntimeException('Invalid PIN code.');
			}

			$transaction = $db->beginTransaction();
			$this->generateProductBalances();
			$row = $db->createCommand(
				'SELECT *
				 FROM inventory_periods
				 WHERE id = :id
				 FOR UPDATE',
				[':id' => $this->id]
			)->queryOne();

			if (!$row)
			{
				throw new \RuntimeException(
					'Inventory period not found.'
				);
			}

			if ($row['status'] !== 'open')
			{
				throw new \RuntimeException(
					'This inventory period is already closed.'
				);
			}

			$currentYear = (int) date('Y');
			$currentMonth = (int) date('n');

			$periodYear = (int) $row['year'];
			$periodMonth = (int) $row['month'];

			if (
				$periodYear > $currentYear ||
				(
					$periodYear === $currentYear &&
					$periodMonth >= $currentMonth
				)
			)
			{
				throw new \RuntimeException(
					'Only previous inventory months can be closed.'
				);
			}

			// Prevent closing periods containing unfinished transactions.
			$draftInCount = (new \yii\db\Query())
				->from('inventory_ins')
				->where([
					'inventory_period_id' => $this->id,
					'status' => 'draft',
				])
				->count('*',$db);

			$draftOutCount = (new \yii\db\Query())
				->from('inventory_outs')
				->where([
					'inventory_period_id' => $this->id,
					'status' => 'draft',
				])
				->count('*',$db);

			if ($draftInCount > 0 || $draftOutCount > 0)
			{
				throw new \RuntimeException(
					'Cannot close this period because it contains draft transactions. ' .
					'Inventory IN drafts: ' . $draftInCount .
					', Inventory OUT drafts: ' . $draftOutCount . '.'
				);
			}

			$affected = $db->createCommand()->update(
				'inventory_periods',
				[
					'status' => 'closed',
					'closed_by' => $userId,
					'closed_at' => date('Y-m-d H:i:s'),
				],
				[
					'id' => $this->id,
					'status' => 'open',
				]
			)->execute();

			if ($affected !== 1)
			{
				throw new \RuntimeException(
					'Unable to close the inventory period.'
				);
			}

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
	
	
	public function generateProductBalances(): void
	{
		$db = static::getDb();

		$start = sprintf('%04d-%02d-01',(int) $this->year,(int) $this->month);
		$end = date('Y-m-d',strtotime($start . ' +1 month'));

		// Assumes inventory_in_items.qty is the incoming quantity column.
		$incoming = (new \yii\db\Query())
			->select([
				'product_id' => 'ii.product_id',
				'opening_qty' => new \yii\db\Expression(
					'SUM(CASE WHEN i.transaction_date < :start THEN ii.qty ELSE 0 END)'
				),
				'in_qty' => new \yii\db\Expression(
					'SUM(CASE WHEN i.transaction_date >= :start AND i.transaction_date < :end THEN ii.qty ELSE 0 END)'
				),
				'opening_value' => new \yii\db\Expression(
					'SUM(CASE WHEN i.transaction_date < :start THEN ii.total_amount ELSE 0 END)'
				),
				'in_value' => new \yii\db\Expression(
					'SUM(CASE WHEN i.transaction_date >= :start AND i.transaction_date < :end THEN ii.total_amount ELSE 0 END)'
				),
			])
			->from(['ii' => 'inventory_in_items'])
			->innerJoin(['i' => 'inventory_ins'],'i.id = ii.inventory_in_id')
			->where(['i.status' => 'posted'])
			->andWhere(['<','i.transaction_date',$end])
			->groupBy('ii.product_id')
			->addParams([':start' => $start,':end' => $end])
			->all($db);

		$outgoing = (new \yii\db\Query())
			->select([
				'product_id' => 'oi.product_id',
				'opening_qty' => new \yii\db\Expression(
					'SUM(CASE WHEN o.transaction_date < :start THEN oi.qty ELSE 0 END)'
				),
				'out_qty' => new \yii\db\Expression(
					'SUM(CASE WHEN o.transaction_date >= :start AND o.transaction_date < :end THEN oi.qty ELSE 0 END)'
				),
				'opening_value' => new \yii\db\Expression(
					'SUM(CASE WHEN o.transaction_date < :start THEN COALESCE(a.total_amount,0) ELSE 0 END)'
				),
				'out_value' => new \yii\db\Expression(
					'SUM(CASE WHEN o.transaction_date >= :start AND o.transaction_date < :end THEN COALESCE(a.total_amount,0) ELSE 0 END)'
				),
			])
			->from(['oi' => 'inventory_out_items'])
			->innerJoin(['o' => 'inventory_outs'],'o.id = oi.inventory_out_id')
			->leftJoin(
				['a' => 'inventory_out_item_allocations'],
				'a.inventory_out_item_id = oi.id'
			)
			->where(['o.status' => 'posted'])
			->andWhere(['<','o.transaction_date',$end])
			->groupBy('oi.product_id')
			->addParams([':start' => $start,':end' => $end])
			->all($db);

		$balances = [];

		foreach (Products::find()->select('id')->asArray()->all($db) as $product)
		{
			$balances[(int) $product['id']] = [
				'opening_qty' => 0,
				'in_qty' => 0,
				'out_qty' => 0,
				'opening_value' => 0,
				'in_value' => 0,
				'out_value' => 0,
			];
		}

		foreach ($incoming as $row)
		{
			$id = (int) $row['product_id'];

			foreach (['opening_qty','in_qty','opening_value','in_value'] as $field)
			{
				$balances[$id][$field] = (float) $row[$field];
			}
		}

		foreach ($outgoing as $row)
		{
			$id = (int) $row['product_id'];

			$balances[$id]['opening_qty'] -= (float) $row['opening_qty'];
			$balances[$id]['out_qty'] = (float) $row['out_qty'];
			$balances[$id]['opening_value'] -= (float) $row['opening_value'];
			$balances[$id]['out_value'] = (float) $row['out_value'];
		}

		foreach ($balances as $productId => $row)
		{
			$closingQty = $row['opening_qty'] + $row['in_qty'] - $row['out_qty'];
			$closingValue = $row['opening_value'] + $row['in_value'] - $row['out_value'];

			$data = [
				'inventory_period_id' => $this->id,
				'product_id' => $productId,
				'opening_qty' => round($row['opening_qty'],3),
				'in_qty' => round($row['in_qty'],3),
				'out_qty' => round($row['out_qty'],3),
				'closing_qty' => round($closingQty,3),
				'opening_value' => round($row['opening_value'],2),
				'in_value' => round($row['in_value'],2),
				'out_value' => round($row['out_value'],2),
				'closing_value' => round($closingValue,2),
			];

			$db->createCommand()->upsert(
				'inventory_period_product_balances',
				$data,
				[
					'opening_qty' => $data['opening_qty'],
					'in_qty' => $data['in_qty'],
					'out_qty' => $data['out_qty'],
					'closing_qty' => $data['closing_qty'],
					'opening_value' => $data['opening_value'],
					'in_value' => $data['in_value'],
					'out_value' => $data['out_value'],
					'closing_value' => $data['closing_value'],
					'updated_at' => date('Y-m-d H:i:s'),
				]
			)->execute();
		}
	}
	
	public function getProductBalancesProvider(): \yii\data\ActiveDataProvider
	{
		$query = (new \yii\db\Query())
			->select([
				'b.*',
				'product_name' => 'p.name',
			])
			->from(['b' => 'inventory_period_product_balances'])
			->innerJoin(['p' => 'products'],'p.id = b.product_id')
			->where(['b.inventory_period_id' => $this->id])
			->andWhere([
				'or',
				['<>','b.in_qty',0],
				['<>','b.out_qty',0],
			])
			->orderBy(['p.name' => SORT_ASC]);

		return new \yii\data\ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 20,
				'pageParam' => 'balance-page',
			],
		]);
	}
	
	public function getProductBalanceSummary(): array
	{
		$summary = (new \yii\db\Query())
			->select([
				'product_count' => 'COUNT(*)',
				'total_in_qty' => 'COALESCE(SUM(in_qty),0)',
				'total_out_qty' => 'COALESCE(SUM(out_qty),0)',
				'total_closing_qty' => 'COALESCE(SUM(closing_qty),0)',
				'total_closing_value' => 'COALESCE(SUM(closing_value),0)',
			])
			->from('inventory_period_product_balances')
			->where(['inventory_period_id' => $this->id])
			->andWhere([
				'or',
				['<>','in_qty',0],
				['<>','out_qty',0],
			])
			->one();

		return $summary ?: [
			'product_count' => 0,
			'total_in_qty' => 0,
			'total_out_qty' => 0,
			'total_closing_qty' => 0,
			'total_closing_value' => 0,
		];
	}

	public function getTransactionSummary(): array
	{
		$db = static::getDb();

		$in = (new \yii\db\Query())
			->select([
				'total' => 'COUNT(*)',
				'posted' => "SUM(CASE WHEN status = 'posted' THEN 1 ELSE 0 END)",
			])
			->from('inventory_ins')
			->where(['inventory_period_id' => $this->id])
			->one($db);

		$out = (new \yii\db\Query())
			->select([
				'total' => 'COUNT(*)',
				'posted' => "SUM(CASE WHEN status = 'posted' THEN 1 ELSE 0 END)",
			])
			->from('inventory_outs')
			->where(['inventory_period_id' => $this->id])
			->one($db);

		return [
			'in_total' => (int) ($in['total'] ?? 0),
			'in_posted' => (int) ($in['posted'] ?? 0),
			'out_total' => (int) ($out['total'] ?? 0),
			'out_posted' => (int) ($out['posted'] ?? 0),
		];
	}
	
	public function getPeriodHistory(int $limit = 50): array
	{
		$db = static::getDb();

		$inHistory = (new \yii\db\Query())
			->select([
				'i.id',
				'i.transaction_date',
				'i.status',
				'i.created_at',
				'i.user_id',
				'u.first_name',
				'u.last_name',
				'department_name' => 'd.name',
			])
			->from(['i' => 'inventory_ins'])
			->leftJoin(['u' => 'user'],'u.id = i.user_id')
			->leftJoin(['d' => 'departments'],'d.id = u.department_id')
			->where(['i.inventory_period_id' => $this->id])
			->orderBy(['i.created_at' => SORT_DESC])
			->limit($limit)
			->all($db);

		$outHistory = (new \yii\db\Query())
			->select([
				'o.id',
				'o.transaction_date',
				'o.status',
				'o.created_at',
				'o.user_id',
				'u.first_name',
				'u.last_name',
				'department_name' => 'd.name',
			])
			->from(['o' => 'inventory_outs'])
			->leftJoin(['u' => 'user'],'u.id = o.user_id')
			->leftJoin(['d' => 'departments'],'d.id = u.department_id')
			->where(['o.inventory_period_id' => $this->id])
			->orderBy(['o.created_at' => SORT_DESC])
			->limit($limit)
			->all($db);

		$history = [];

		foreach ($inHistory as $row)
		{
			$fullName = trim(
				($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')
			);

			$department = $row['department_name'] ?? '';

			$history[] = [
				'type' => 'in',
				'title' => 'Inventory IN #' . $row['id'],
				'description' => 'Status: ' . ucfirst($row['status']),
				'user_name' => $fullName ?: 'Unknown User',
				'department' => $department,
				'date' => $row['created_at'] ?: $row['transaction_date'],
				'url' => ['/inventory-in/view','id' => $row['id']],
			];
		}

		foreach ($outHistory as $row)
		{
			$history[] = [
				'type' => 'out',
				'title' => 'Inventory OUT #' . $row['id'],
				'description' => 'Status: ' . ucfirst($row['status']),
				'date' => $row['created_at'] ?: $row['transaction_date'],
				'url' => ['/inventory-out/view','id' => $row['id']],
			];
		}

		if ($this->status === 'closed' && $this->closed_at)
		{
			$history[] = [
				'type' => 'closed',
				'title' => 'Inventory Period Closed',
				'description' => 'Monthly inventory balances finalized',
				'date' => $this->closed_at,
				'url' => null,
			];
		}

		usort($history,function ($a,$b)
		{
			return strcmp((string) $b['date'],(string) $a['date']);
		});

		return array_slice($history,0,$limit);
	}
	
	public function getClosedByUser()
	{
		return $this->hasOne(User::class, ['id' => 'closed_by']);
	}
	
	public function getClosedByDisplayName(): string
	{
		$user = $this->closedByUser;

		if ($user === null)
		{
			return 'N/A';
		}

		$fullName = trim(
			($user->first_name ?? '') . ' ' . ($user->last_name ?? '')
		);

		$department = $user->department->name ?? '';

		if ($fullName === '')
		{
			$fullName = $user->username;
		}

		return $fullName . ($department !== '' ? ' (' . $department . ')' : '');
	}
}
