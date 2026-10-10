<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Products;

class ProductSearch extends Products
{
    public function rules()
    {
        return [
            [[ 'product_type_id', 'unit_id'], 'integer'], // 🛠️ Tiyaking integer filters ang mga ito
            [['name', 'description', 'model', 'created_at'], 'safe'],
            [[ 'installation_price'], 'number'],
        ];
    }

    public function search($params)
    {
        $query = Products::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // 🛠️ Strict matching para sa mga Dropdown IDs
        $query->andFilterWhere([
            'product_type_id' => $this->product_type_id,  // Filter batay sa piniling Type dropdown
            'unit_id' => $this->unit_id,  // Filter batay sa piniling Unit dropdown
            'installation_price' => $this->installation_price,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'model', $this->model]);

        return $dataProvider;
    }
}
