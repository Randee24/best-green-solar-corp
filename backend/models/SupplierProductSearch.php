<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class SupplierProductSearch extends SupplierProducts
{
    public $product_name;
    public $product_model;

    public function rules()
    {
        return [
            [['product_name','product_model','supplier_product_code'],'safe'],
            [['supplier_price'],'number'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params,int $supplierId)
    {
        // Select the latest supplier_products record for each product.
        $latestIds = (new Query())
            ->select('MAX(sp2.id)')
            ->from(['sp2' => 'supplier_products'])
            ->where(['sp2.supplier_id' => $supplierId])
            ->groupBy('sp2.product_id');

        $query = SupplierProducts::find()
            ->alias('sp')
            ->innerJoin(['p' => 'products'],'p.id = sp.product_id')
            ->where([
                'sp.supplier_id' => $supplierId,
            ])
            ->andWhere(['sp.id' => $latestIds])
            ->with(['product']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
                'attributes' => [
                    'id' => [
                        'asc' => ['sp.id' => SORT_ASC],
                        'desc' => ['sp.id' => SORT_DESC],
                    ],
                    'product_name' => [
                        'asc' => ['p.name' => SORT_ASC],
                        'desc' => ['p.name' => SORT_DESC],
                    ],
                    'product_model' => [
                        'asc' => ['p.model' => SORT_ASC],
                        'desc' => ['p.model' => SORT_DESC],
                    ],
                    'supplier_product_code' => [
                        'asc' => ['sp.supplier_product_code' => SORT_ASC],
                        'desc' => ['sp.supplier_product_code' => SORT_DESC],
                    ],
                    'supplier_price' => [
                        'asc' => ['sp.supplier_price' => SORT_ASC],
                        'desc' => ['sp.supplier_price' => SORT_DESC],
                    ],
                    'created_at' => [
                        'asc' => ['sp.created_at' => SORT_ASC],
                        'desc' => ['sp.created_at' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate())
        {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'sp.supplier_price' => $this->supplier_price,
        ]);

        $query->andFilterWhere(['like','p.name',$this->product_name])
            ->andFilterWhere(['like','p.model',$this->product_model])
            ->andFilterWhere(['like','sp.supplier_product_code',$this->supplier_product_code]);

        return $dataProvider;
    }
}