<?php

namespace backend\models;

use backend\models\Accesses;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AccessSearch extends Accesses
{
    public function rules()
    {
        return [
            [['id', 'role_id', 'department_id', 'page_id', 'status'], 'integer'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Accesses::find()
            ->with(['role', 'department', 'page']);

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
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'role_id' => $this->role_id,
            'department_id' => $this->department_id,
            'page_id' => $this->page_id,
            'status' => $this->status,
        ]);

        return $dataProvider;
    }
}
