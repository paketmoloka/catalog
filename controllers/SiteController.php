<?php

namespace app\controllers;

use app\models\Tokens;
use app\models\User;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\Response;
use app\models\LoginForm;

class SiteController extends Controller
{

    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(),
            ],
            'only' => ['logout']
        ];
        return $behaviors;
    }

    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return [
            'status_code' => 200,
            'message' => 'API Catalog v1'
        ];
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->load(Yii::$app->request->post());
        if ($token = $model->login()) {
            return [
                'token' => $token->token,
                'expired' => date(DATE_RFC3339, $token->expired),
            ];
        } else {
            return $model;
        }
    }

    public function actionRegister() {
        $user = new User();
        $user->role = User::USER;
        if ($user->load(Yii::$app->request->post()) && $user->create()) {
            return ['status_code' => 200, 'message' => 'ok', 'user' => $user];
        } else {
            return ['status_code' => 400, 'message' => 'error', 'errors' => $user->errors];
        }
    }

    public function actionLogout() {
        /** @var Tokens $token */
        foreach (Yii::$app->user->identity->tokens as $token) {
            $token->delete();
        }
        Yii::$app->user->logout();

        return [
            'status_code' => 200,
            'message' => 'Successfully logged out'
        ];
    }

}
