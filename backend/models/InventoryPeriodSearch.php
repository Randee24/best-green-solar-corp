<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class InventoryPeriodSearch extends InventoryPeriods
{
    public function rules()
    {
        return [
            [['id','year','month'],'integer'],
            [['status'],'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = InventoryPeriods::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'year' => SORT_DESC,
                    'month' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'year' => $this->year,
            'month' => $this->month,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}