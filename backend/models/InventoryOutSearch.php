<?php

namespace backend\models;

use backend\models\InventoryOuts;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class InventoryOutSearch extends InventoryOuts
{
    public $project_name;

    public function rules()
    {
        return [
            [['id', 'inventory_period_id'], 'integer'],
            [['project_name', 'status'], 'safe'],
            [['transaction_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = InventoryOuts::find()
            ->alias('inventory_outs');

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
            'inventory_outs.id' => $this->id,
            'inventory_outs.inventory_period_id' => $this->inventory_period_id,
            'inventory_outs.status' => $this->status,
        ]);

        // Project name filter
        if ($this->project_name !== null && trim($this->project_name) !== '')
        {
            $query->innerJoin(
                'projects project',
                'project.id = inventory_outs.project_id'
            );

            $query->andWhere([
                'like',
                'project.name',
                trim($this->project_name),
            ]);
        }

        // Transaction date filter
        if ($this->transaction_date !== null && $this->transaction_date !== '')
        {
            $query->andWhere([
                '>=',
                'inventory_outs.transaction_date',
                $this->transaction_date . ' 00:00:00',
            ]);

            $query->andWhere([
                '<',
                'inventory_outs.transaction_date',
                date('Y-m-d',strtotime($this->transaction_date . ' +1 day')) . ' 00:00:00',
            ]);
        }

        return $dataProvider;
    }
}
