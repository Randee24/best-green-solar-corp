<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ProductPriceChanges;

/**
 * ProductPriceChangeSearch represents the model behind the search form of `app\models\ProductPriceChanges`.
 */
class ProductPriceChangeSearch extends ProductPriceChanges
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'product_id','supplier_product_id', 'user_id'], 'integer'],
            [['old_price', 'new_price', 'changed_at', 'created_at'], 'safe'],
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
        $query = ProductPriceChanges::find();

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
            'product_id' => $this->product_id,
            'supplier_product_id' => $this->supplier_product_id,
            'user_id' => $this->user_id,
            'changed_at' => $this->changed_at,
            'created_at' => $this->created_at,
        ]);

        $query->andFilterWhere(['like', 'old_price', $this->old_price])
            ->andFilterWhere(['like', 'new_price', $this->new_price]);

        return $dataProvider;
    }
}
