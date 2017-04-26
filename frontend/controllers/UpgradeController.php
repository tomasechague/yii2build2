<?php

namespace frontend\controllers;

use yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\PermisosHelpers;
use common\models\RegistrosHelpers;
use frontend\models\Perfil;

class UpgradeController extends \yii\web\Controller {

    public function behaviors() {
        return ['access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action) {
                            return PermisosHelpers::requerirEstado('Activo');
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionIndex() {
        $persona = Perfil::find()->where(['user_id'=>Yii::$app->user->identity->id])->one();
        return $this->render('index',['persona'=>$persona]);
    }

}
