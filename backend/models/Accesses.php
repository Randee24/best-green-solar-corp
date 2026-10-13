<?php

namespace backend\models;

use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class Accesses extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%accesses}}';
    }

    public function rules()
    {
        return [
            [['role_id','department_id','page_id'],'required'],
            [['role_id','department_id','page_id','status'],'integer'],
            ['status','default','value' => 1],
            ['status','in','range' => [0,1]],
            [['role_id','department_id','page_id'],'unique','targetAttribute' => ['role_id','department_id','page_id'],'message' => 'This role, department and page combination already exists.'],
            ['role_id','exist','targetClass' => Roles::class,'targetAttribute' => 'id'],
            ['department_id','exist','targetClass' => Departments::class,'targetAttribute' => 'id'],
            ['page_id','exist','targetClass' => Pages::class,'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role',
            'department_id' => 'Department',
            'page_id' => 'Page',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public function getRole()
    {
        return $this->hasOne(Roles::class,['id' => 'role_id']);
    }

    public function getDepartment()
    {
        return $this->hasOne(Departments::class,['id' => 'department_id']);
    }

    public function getPage()
    {
        return $this->hasOne(Pages::class,['id' => 'page_id']);
    }

    public static function getIndexDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => self::find()->with(['role','department','page'])->orderBy(['id' => SORT_DESC]),
            'pagination' => ['pageSize' => 20],
        ]);
    }

    public static function getFormOptions(): array
    {
        return [
            'roleOptions' => (new Query())->select(['name','id'])->from('{{%roles}}')->indexBy('id')->column(),
            'departmentOptions' => (new Query())->select(['name','id'])->from('{{%departments}}')->indexBy('id')->column(),
            'pageOptions' => (new Query())->select(['name','id'])->from('{{%pages}}')->indexBy('id')->column(),
        ];
    }
	
	
	public static function getRoleOptions(): array
	{
		return ArrayHelper::map(
			Roles::find()->orderBy(['name' => SORT_ASC])->all(),
			'id',
			'name'
		);
	}

	public static function getDepartmentOptions(): array
	{
		return ArrayHelper::map(
			Departments::find()->orderBy(['name' => SORT_ASC])->all(),
			'id',
			'name'
		);
	}

	public static function getPageOptions(): array
	{
		return ArrayHelper::map(
			Pages::find()->orderBy(['name' => SORT_ASC])->all(),
			'id',
			'name'
		);
	}

	
}
