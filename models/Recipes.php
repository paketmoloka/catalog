<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "recipes".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $user_id
 * @property int $create_time
 * @property int $update_time
 */
class Recipes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'recipes';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['description'], 'string'],
            [['user_id', 'create_time', 'update_time'], 'default', 'value' => null],
            [['user_id', 'create_time', 'update_time'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'user_id' => 'User ID',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * @return array|bool
     */
    public function create() {
        $this->user_id = Yii::$app->user->identity->id;
        $this->create_time = time();
        $this->update_time = time();
        if ($this->save()) {
            $this->uploadFiles();
            return true;
        }
        return false;
    }

    /**
     * @return array|bool
     */
    public function updateRecord() {
        $this->update_time = time();
        if ($this->validate() && $this->save()) {
            return true;
        }
        return false;
    }

    /**
     * @return array|bool
     */
    public function deleteRecord() {
        if ($this->delete()) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getFiles() {
        $result = [];
        $files = Files::find()->where(['onj_type' => Files::TYPE_RECIPE_FILE, 'obj_id' => $this->id])->all();
        foreach ($files as $file) {
            /** @var $file Files */
            $result[$file->id] = [
                'fileName' => $file->name,
                'path' => $file->path,
                'md5' => $file->md5
            ];
        }
        return $result;
    }

    public function uploadFiles() {

        $dirName = Yii::$app->user->identity->id;

        $ds = DIRECTORY_SEPARATOR;

        $ufolder = Yii::getAlias('@webroot') . $ds . 'uploads' . $ds . 'temp' . $ds . $dirName . $ds; //директория откуда надо скопировать файлы
        if (file_exists($ufolder)){
            $files = scandir($ufolder, 1);
            foreach ($files as $file) {
                //Get extension of file
                if($file != '.' && $file != '..' && $file{0}!='.'){
                    $parts = explode('.', $file);
                    $ext = '.' . $parts[count($parts)-1]; //расширение файла

                    /** Относительный путь директории куда надо скопировать файлы */
                    $relative_folder = $ds . 'uploads' . $ds . "files" . $ds;

                    /** Абсолютный путь директории куда надо скопировать файлы (этот путь нельзя показывать юзеру) */
                    $absolute_folder = Yii::getAlias('@webroot') . $relative_folder;

                    if (!file_exists($absolute_folder))
                        mkdir($absolute_folder, 0777);

                    $md5_name  = md5($file . time() . rand(0,10000)) . $ext;
                    rename($ufolder . $file, $absolute_folder . $md5_name);
                    chmod($absolute_folder . $md5_name, 0777);

                    $f = new Files;
                    $f->processSave($md5_name, $file, $relative_folder . $md5_name, $this->id, Files::TYPE_RECIPE_FILE);
                }
            }

            /** Удаляем временную (сессионную) директорию */
            rmdir($ufolder);
            return true;
        }
        return false;
    }

}
