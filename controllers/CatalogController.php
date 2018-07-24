<?php

namespace app\controllers;

use app\models\Recipes;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;

class CatalogController extends Controller
{

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
            ],
            'only' => ['list','create', 'update', 'delete']
        ];
        return $behaviors;
    }

    public $enableCsrfValidation = false;


    /**
     * @return array
     */
    public function actionCreate()    {
        $model = new Recipes();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->create()) {
                return [
                    'status_code' => 200,
                    'data' => $model,
                ];
            }
        }
        return [
            'status_code' => 400,
            'message' => 'No post data!',
            'errors' => $model->errors
        ];
    }

    public function actionList()
    {
        $data = [];
        $recipes = Recipes::find()->where(['user_id' => Yii::$app->user->identity->id])->all();
        foreach ($recipes as $recipe) {
            /** @var $recipe Recipes */
            $data[$recipe->id] = [
                'recipe' => $recipe,
                'files' => $recipe->files,
            ];
        }
        return [
            'status_code' => 200,
            'data' => $data,
        ];
    }

    public function actionUpdate() {
        $model = Recipes::findOne(Yii::$app->request->post('Recipes')['id']);
        if ($model) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->create()) {
                    return [
                        'status_code' => 200,
                        'data' => $model,
                        'files' => $model->files,
                    ];
                }
            }
        }
        return [
            'status_code' => 400,
            'message' => 'No post data!',
        ];
    }

    public function actionDelete() {
        $model = Recipes::findOne(Yii::$app->request->post('Recipes')['id']);
        if ($model) {
            if ($model->deleteRecord()) {
                return [
                    'status_code' => 200,
                ];
            }
        }
        return [
            'status_code' => 400,
            'message' => 'No post data!',
        ];
    }

}
