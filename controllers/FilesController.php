<?php
namespace app\controllers;

use app\models\Files;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Session;
use yii\web\UploadedFile;


class FilesController extends \yii\rest\Controller {
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['authMethods'] = [
            HttpBasicAuth::className(),
            HttpBearerAuth::className(),
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }


    public function actionGetSessionId() {
        $session = new Session;
        $session->open();
        $session['cs_'.Yii::$app->user->id] = md5(time().rand(0,10000));
        return $session['cs_'.Yii::$app->user->id];
    }


    public function actionUpload() {
        if (!empty($_FILES['file']['tmp_name'])) {
            $file = $_FILES['file']['name'];
            /**Есть гон что иногда папка temp удаляется и происходит бардак, поэтому даже если он похерился его надо создать */
            $temp_folder = Yii::getAlias('@webroot') .
                DIRECTORY_SEPARATOR . 'uploads' .
                DIRECTORY_SEPARATOR . 'temp';
            $dir = Yii::$app->user->identity->id;


            $session_folder = Yii::getAlias('@webroot') .
                DIRECTORY_SEPARATOR . 'uploads' .
                DIRECTORY_SEPARATOR . 'temp' .
                DIRECTORY_SEPARATOR . $dir;
            if (!file_exists($session_folder))
                mkdir($session_folder, 0777);
                chmod($session_folder, 0777);

            $upload_folder = $session_folder . DIRECTORY_SEPARATOR;

            if (!file_exists($upload_folder))
                mkdir($upload_folder, 0777);
                chmod($upload_folder, 0777);
            $targetFile =  $upload_folder . $file;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
                chmod($targetFile, 0777);
                return ['statusCode' => 200, 'file' => utf8_encode($file)];
            } else {
                ['statusCode' => 400, 'file' => utf8_encode($file)];
            }
        }
    }


    public function actionDeleteFile() {
        if (Yii::$app->request->post('filename')) {
            $dir = Yii::$app->user->identity->id;
            $targetFile = Yii::getAlias('@app') .
                DIRECTORY_SEPARATOR . 'uploads' .
                DIRECTORY_SEPARATOR . 'temp' .
                DIRECTORY_SEPARATOR . $dir .
                DIRECTORY_SEPARATOR . Yii::$app->request->post('filename');

            return unlink($targetFile) ? ['statusCode' => 200] : ['statusCode' => 400];
        }

        return ['statusCode' => 500];
    }

    public function actionDownloadFile() {
        Yii::$app->response->clearOutputBuffers();
        $file = Files::findOne(Yii::$app->request->get('id'));
        if (!empty($file)) {
            Yii::$app->response->sendFile(Yii::getAlias('@app')  .$file->path, $file->name);
        } else {
            throw new NotFoundHttpException('Error downloading file');
        }
    }


}
?>

