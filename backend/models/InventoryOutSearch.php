<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\InventoryOuts;

/**
 * InventoryOutSearch represents the model behind the search form of `app\models\InventoryOuts`.
 */
class InventoryOutSearch extends InventoryOuts
{
	public $project_name;

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function rules()
    {
        return [
            [['id','project_id','inventory_period_id'],'integer'],
            [['transaction_date','status','project_name'],'safe'],
        ];
    }


    public function search($params)
    {
        $query = InventoryOuts::find()
            ->alias('o')
            ->joinWith(['project p']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ],
        ]);

        $dataProvider->sort->attributes['project_name'] = [
            'asc' => ['p.name' => SORT_ASC],
            'desc' => ['p.name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'o.id' => $this->id,
            'o.project_id' => $this->project_id,
            'o.inventory_period_id' => $this->inventory_period_id,
            'o.transaction_date' => $this->transaction_date,
            'o.status' => $this->status,
        ]);

        $query->andFilterWhere(['like','p.name',$this->project_name]);

        return $dataProvider;
    }
}
