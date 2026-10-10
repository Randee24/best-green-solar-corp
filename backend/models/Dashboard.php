<?php
namespace backend\models;
use Yii;
use yii\base\Model;
use yii\db\Query;

class Dashboard extends Model
{
    public static function getMonthlySummary(int $year,int $month): array
    {
        $db = Yii::$app->db;

        $startDate = sprintf('%04d-%02d-01',$year,$month);
        $endDate = date('Y-m-d',strtotime($startDate . ' +1 month'));

        $inventoryIn = (new Query())
            ->from('inventory_ins')
            ->where(['status' => 'posted'])
            ->andWhere(['>=','transaction_date',$startDate])
            ->andWhere(['<','transaction_date',$endDate]);

        $inventoryOut = (new Query())
            ->from('inventory_outs')
            ->where(['status' => 'posted'])
            ->andWhere(['>=','transaction_date',$startDate])
            ->andWhere(['<','transaction_date',$endDate]);

        $totalIn = (clone $inventoryIn)->count('*',$db);
        $totalOut = (clone $inventoryOut)->count('*',$db);

        // Sum incoming purchase values.
        $incomingValue = (new Query())
            ->from(['ii' => 'inventory_in_items'])
            ->innerJoin(
                ['i' => 'inventory_ins'],
                'i.id = ii.inventory_in_id'
            )
            ->where(['i.status' => 'posted'])
            ->andWhere(['>=','i.transaction_date',$startDate])
            ->andWhere(['<','i.transaction_date',$endDate])
            ->sum('ii.total_amount',$db);

        // Sum outgoing FIFO costs.
        $outgoingCost = (new Query())
            ->from(['a' => 'inventory_out_item_allocations'])
            ->innerJoin(
                ['oi' => 'inventory_out_items'],
                'oi.id = a.inventory_out_item_id'
            )
            ->innerJoin(
                ['o' => 'inventory_outs'],
                'o.id = oi.inventory_out_id'
            )
            ->where(['o.status' => 'posted'])
            ->andWhere(['>=','o.transaction_date',$startDate])
            ->andWhere(['<','o.transaction_date',$endDate])
            ->sum('a.total_amount',$db);

        $draftIn = (new Query())
            ->from('inventory_ins')
            ->where(['status' => 'draft'])
            ->andWhere(['>=','transaction_date',$startDate])
            ->andWhere(['<','transaction_date',$endDate])
            ->count('*',$db);

        $draftOut = (new Query())
            ->from('inventory_outs')
            ->where(['status' => 'draft'])
            ->andWhere(['>=','transaction_date',$startDate])
            ->andWhere(['<','transaction_date',$endDate])
            ->count('*',$db);

        return [
            'total_in' => (int) $totalIn,
            'total_out' => (int) $totalOut,
            'incoming_value' => (float) ($incomingValue ?: 0),
            'outgoing_cost' => (float) ($outgoingCost ?: 0),
            'draft_in' => (int) $draftIn,
            'draft_out' => (int) $draftOut,
        ];
    }

    public static function getMonthlyActivity(int $year,int $month): array
    {
        $db = Yii::$app->db;

        $startDate = sprintf('%04d-%02d-01',$year,$month);
        $endDate = date('Y-m-d',strtotime($startDate . ' +1 month'));

        $result = [];

        for ($week = 1; $week <= 5; $week++)
        {
            $result[$week] = [
                'week' => 'Week ' . $week,
                'incoming' => 0,
                'outgoing' => 0,
            ];
        }

        $tables = [
            'incoming' => 'inventory_ins',
            'outgoing' => 'inventory_outs',
        ];

        foreach ($tables as $key => $table)
        {
            $rows = (new Query())
                ->select([
                    'week_number' => new \yii\db\Expression(
                        'FLOOR((DAY(transaction_date) - 1) / 7) + 1'
                    ),
                    'total' => new \yii\db\Expression('COUNT(*)'),
                ])
                ->from($table)
                ->where(['status' => 'posted'])
                ->andWhere(['>=','transaction_date',$startDate])
                ->andWhere(['<','transaction_date',$endDate])
                ->groupBy(['week_number'])
                ->all($db);

            foreach ($rows as $row)
            {
                $week = (int) $row['week_number'];

                if (isset($result[$week]))
                {
                    $result[$week][$key] = (int) $row['total'];
                }
            }
        }

        return array_values($result);
    }

    public static function getRecentTransactions(int $year,int $month): array
    {
        $startDate = sprintf('%04d-%02d-01',$year,$month);
        $endDate = date('Y-m-d',strtotime($startDate . ' +1 month'));

        $incoming = (new Query())
            ->select([
                'id',
                'transaction_date',
                'status',
                new \yii\db\Expression("'IN' AS transaction_type"),
            ])
            ->from('inventory_ins')
            ->where(['>=','transaction_date',$startDate])
            ->andWhere(['<','transaction_date',$endDate]);

        $outgoing = (new Query())
            ->select([
                'id',
                'transaction_date',
                'status',
                new \yii\db\Expression("'OUT' AS transaction_type"),
            ])
            ->from('inventory_outs')
            ->where(['>=','transaction_date',$startDate])
            ->andWhere(['<','transaction_date',$endDate]);

        return (new Query())
            ->from(['t' => $incoming->union($outgoing,true)])
            ->orderBy([
                'transaction_date' => SORT_DESC,
                'id' => SORT_DESC,
            ])
            ->limit(10)
            ->all();
    }
}
