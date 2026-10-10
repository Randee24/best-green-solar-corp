<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\InventoryIns;

/**
 * InventoryInSearch represents the model behind the search form of `app\models\InventoryIns`.
 */
class InventoryInSearch extends InventoryIns
{
    public $supplier_name;
    public $period_name;

    public function rules()
    {
        return [
            [['id','supplier_id','inventory_period_id'],'integer'],
            [['transaction_date','status','supplier_name','period_name'],'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = InventoryIns::find()
            ->alias('i')
            ->joinWith(['supplier s','inventoryPeriod p'])
            ->with(['inventoryInItems.product']);

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

        $dataProvider->sort->attributes['supplier_name'] = [
            'asc' => ['s.name' => SORT_ASC],
            'desc' => ['s.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['period_name'] = [
            'asc' => ['p.year' => SORT_ASC,'p.month' => SORT_ASC],
            'desc' => ['p.year' => SORT_DESC,'p.month' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'i.id' => $this->id,
            'i.supplier_id' => $this->supplier_id,
            'i.inventory_period_id' => $this->inventory_period_id,
            'i.transaction_date' => $this->transaction_date,
            'i.status' => $this->status,
        ]);

        $query->andFilterWhere(['like','s.name',$this->supplier_name]);

        if (!empty($this->period_name))
        {
            $query->andWhere(
                "CONCAT(p.year,'-',LPAD(p.month,2,'0')) LIKE :period",
                [':period' => '%' . $this->period_name . '%']
            );
        }

        return $dataProvider;
    }
}
