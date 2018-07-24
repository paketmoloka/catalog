<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tokens".
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property int $expired
 */
class Tokens extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tokens';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'token', 'expired'], 'required'],
            [['user_id', 'expired'], 'default', 'value' => null],
            [['user_id', 'expired'], 'integer'],
            [['token'], 'string'],
            [['token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'token' => 'Token',
            'expired' => 'Expired',
        ];
    }

    public function generateToken($expire)
    {
        $this->expired = $expire;
        $this->token = \Yii::$app->security->generateRandomString();
    }

}
