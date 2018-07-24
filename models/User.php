<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\web\NotFoundHttpException;

/**
 * Class User
 * @package app\modules\user\models
 *
 * @property integer $id
 * @property string $login логин пользователя
 * @property string $hash хеш
 * @property string $role роль пользователя
 * @property integer $create_time время создания
 * @property integer $update_time время обновления
 */

class User extends ActiveRecord implements IdentityInterface {

    /** @var string переменная повтора пароля. Используется при восстановлении */
    public $repeat_pass;
    /** @var string переменная нового пароля. Используется при восстановлении */
    public $new_pass;

    public $old_pass;

    /** Роли пользователей */
    const USER = 100;
    const SUPERADMIN = 200;

    /** Scenarios */
    const SCENARIO_CREATE = 'create';


    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['role', 'login', 'new_pass', 'repeat_pass', 'hash'];
        return $scenarios;
    }

    public static function tableName() {
        return 'users';
    }

    public function rules() {
        return [
            [['login', 'hash', 'role', 'create_time', 'update_time'], 'required'],
            [['new_pass', 'repeat_pass'], 'required', 'on' => self::SCENARIO_CREATE],
            [['login'], 'unique', 'message' => 'Пользователь с таким номером уже зарегистрирован'],
            [['new_pass', 'repeat_pass'], 'string', 'min' => 3],
            [['repeat_pass'], 'compare', 'compareAttribute' => 'new_pass', 'message' => 'Пароли не совподают.'],
            [['create_time', 'update_time', 'role'], 'integer'],
            [['login', 'new_pass', 'repeat_pass', 'hash'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'login' => 'Логин',
            'old_pass' => 'Пароль',
            'create_time' => 'Дата создания',
            'update_time' => 'Дата обновления',
            'role' => 'Роль',
            'repeat_pass' => 'Повторите пароль',
            'new_pass' => 'Новый пароль',
        ];
    }

    /**
     * @param int|string $id
     * @return null|static
     */
    public static function findIdentity($id) {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        $token = Tokens::find()->where(['token' => $token])->one();
        if ($token) {
            return User::findOne($token->user_id);
        } else {
            throw new NotFoundHttpException('Page not Found');
        }
    }

    /**
     * Finds user by username
     *
     * @param string $login
     * @return static|null
     */
    public static function findByUsername($login) {
        return static::findOne(['login' => $login]);
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->login;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password) {
        if (Yii::$app->getSecurity()->validatePassword($password, $this->hash)) return true;
        return false;
    }



    public function create() {
        $this->hash = Yii::$app->getSecurity()->generatePasswordHash($this->new_pass);
        $this->create_time= time();
        $this->update_time = time();
        if ($this->validate() && $this->save()) {
            return true;
        }

        return false;
    }

    public function updateRecord() {
        $this->hash = Yii::$app->getSecurity()->generatePasswordHash($this->new_pass);
        $this->update_time = time();
        if ($this->save()) {
            return true;
        }

        return false;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens() {
        return $this->hasMany(Tokens::className(), ['user_id' => 'id']);
    }

}