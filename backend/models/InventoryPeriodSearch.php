<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class InventoryPeriodSearch extends InventoryPeriods
{
    public function rules()
    {
        return [
            [['id','month','year'],'integer'],
            [['status'],'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = InventoryPeriods::find()
            ->orderBy([
                'year' => SORT_DESC,
                'month' => SORT_DESC,
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'month' => $this->month,
            'year' => $this->year,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
