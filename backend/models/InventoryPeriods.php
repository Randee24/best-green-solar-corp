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
}
