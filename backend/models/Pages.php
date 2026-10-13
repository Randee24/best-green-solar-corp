<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "pages".
 *
 * @property int $id
 * @property string $name
 * @property string $model
 * @property int $status
 * @property string $created_at
 */
class Pages extends \yii\db\ActiveRecord
{
	public const STATUS_INACTIVE = 0;
	public const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'default', 'value' => 1],
            [['name', 'model'], 'required'],
            [['status'], 'integer'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['model'], 'string', 'max' => 150],
            [['model'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'model' => 'Model',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
	
	
	public function getStatusLabel(): string
	{
		return self::getStatusOptions()[(int) $this->status] ?? 'Unknown';
	}
	public static function getStatusOptions(): array
	{
		return [
			self::STATUS_ACTIVE => 'Active',
			self::STATUS_INACTIVE => 'Inactive',
		];
	}

	

}
