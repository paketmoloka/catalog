<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "files".
 *
 * @property int $id
 * @property int $obj_id
 * @property int $onj_type
 * @property string $name
 * @property string $md5
 * @property string $path
 * @property int $create_time
 */
class Files extends \yii\db\ActiveRecord
{

    const TYPE_RECIPE_FILE = 100;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'files';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obj_id', 'onj_type', 'name', 'md5', 'path', 'create_time'], 'required'],
            [['obj_id', 'onj_type', 'create_time'], 'default', 'value' => null],
            [['obj_id', 'onj_type', 'create_time'], 'integer'],
            [['name', 'md5', 'path'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'obj_id' => 'Obj ID',
            'onj_type' => 'Onj Type',
            'name' => 'Name',
            'md5' => 'Md5',
            'path' => 'Path',
            'create_time' => 'Create Time',
        ];
    }

    /**
     * Сохранение данных о залитом файле.
     * Сохранение модели происзодит только здесь! Нигде больше
     * Поэтому работать только с этой функцией.
     * @param $md5
     * @param $name
     * @param $path
     * @param $obj_id
     * @param $obj_type
     * @return bool
     */
    public function processSave($md5, $name, $path, $obj_id, $obj_type) {
        $this->md5 = $md5;
        $this->name = $name;
        $this->path = $path;
        $this->obj_id = $obj_id;
        $this->onj_type = $obj_type;
        $this->create_time = time();

        return $this->save() ? true : false;
    }
}
