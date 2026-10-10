<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\InventoryOutItems;

/**
 * InventoryOutItemSearch represents the model behind the search form of `app\models\InventoryOutItems`.
 */
class InventoryOutItemSearch extends InventoryOutItems
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'inventory_out_id', 'product_id', 'qty'], 'integer'],
            //[['amount','total_amount'], 'number'],
            [['remarks', 'created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = InventoryOutItems::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'inventory_out_id' => $this->inventory_out_id,
            'product_id' => $this->product_id,
            //'amount' => $this->amount,
            //'total_amount' => $this->total_amount,
            'qty' => $this->qty,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
