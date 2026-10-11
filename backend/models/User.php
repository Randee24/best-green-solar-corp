<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property int $role_id
 * @property int $department_id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $verification_token
 */
class User extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password_reset_token', 'verification_token'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 10],
            [['first_name', 'last_name', 'email', 'role_id', 'department_id', 'username', 'auth_key', 'password_hash'], 'required'],
            [['role_id', 'department_id', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['first_name','pin_code', 'last_name'], 'string', 'max' => 50],
            [['email', 'username', 'password_hash', 'password_reset_token', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'pin_code' => 'PIN Code',
            'email' => 'Email',
            'role_id' => 'Role ID',
            'department_id' => 'Department ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'verification_token' => 'Verification Token',
        ];
    }
	
	
	public function setPinCode(string $pin): void
	{
		if (!preg_match('/^[0-9]{4,8}$/',$pin))
		{
			throw new \InvalidArgumentException(
				'PIN must contain 4 to 8 digits.'
			);
		}

		$this->pin_code = Yii::$app->security->generatePasswordHash($pin);
	}

	public function verifyPinCode(string $pin): bool
	{
		if (empty($this->pin_code))
		{
			return false;
		}

		return Yii::$app->security->validatePassword(
			$pin,
			$this->pin_code
		);
	}


}
