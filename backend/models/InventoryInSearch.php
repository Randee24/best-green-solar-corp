<?php

namespace backend\models;

use backend\models\InventoryIns;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class InventoryInSearch extends InventoryIns
{
    public $supplier_name;

    public function rules()
    {
        return [
            [['id', 'inventory_period_id'], 'integer'],
            [['supplier_name', 'status'], 'safe'],
			[['transaction_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = InventoryIns::find()
            ->alias('inventory_ins');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            $query->andWhere('0=1');

            return $dataProvider;
        }

        // Reference, Inventory Period and Status
        $query->andFilterWhere([
            'inventory_ins.id' => $this->id,
            'inventory_ins.inventory_period_id' => $this->inventory_period_id,
            'inventory_ins.status' => $this->status,
        ]);

        // Supplier name filter
        if ($this->supplier_name !== null && trim($this->supplier_name) !== '')
        {
            $query->innerJoin(
                'suppliers supplier',
                'supplier.id = inventory_ins.supplier_id'
            );

            $query->andWhere([
                'like',
                'supplier.name',
                trim($this->supplier_name),
            ]);
        }

       // Transaction date filter
        if ($this->transaction_date !== null && $this->transaction_date !== '')
        {
            $query->andWhere([
                '>=',
                'inventory_ins.transaction_date',
                $this->transaction_date . ' 00:00:00',
            ]);

            $query->andWhere([
                '<',
                'inventory_ins.transaction_date',
                date('Y-m-d',strtotime($this->transaction_date . ' +1 day')) . ' 00:00:00',
            ]);
        }


        return $dataProvider;
    }
}
