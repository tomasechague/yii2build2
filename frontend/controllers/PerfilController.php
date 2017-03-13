<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Perfil;
use frontend\models\search\PerfilSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\PermisosHelpers;
use common\models\RegistrosHelpers;

/**
 * PerfilController implements the CRUD actions for Perfil model.
 */
class PerfilController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access'=> [
              'class' => \yii\filters\AccessControl::className(),
              'only'=> ['index','view', 'create', 'update','delete'],
              'rules'=>[
                  [
                      'actions' => ['index','view','create','update','delete'],
                      'allow'=> true,
                      'roles'=>['@'],//significa que tiene que estar logueado
                  ],
              ],  
            ],
            'verbs'=>[
                'class'=> VerbFilter::className(),
                'actions'=>[
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Perfil models.
     * @return mixed
     */
    public function actionIndex()
    {
        //Usertiene verifica si tiene un registro en el modelo especificado, si tiene devuelve el ID
        if($exist = RegistrosHelpers::userTiene('perfil')){
            return $this->render('view',[
               'model' => $this->findModel($exist),
            ]);
        }else{
            return $this->redirect(['create']);
        }
    }

    /**
     * Displays a single Perfil model.
     * @param integer $id
     * @return mixed
     */
    public function actionView()
    {
       if($exist = RegistrosHelpers::userTiene('perfil')){
           return $this->render('view',[
              'model'=> $this->findModel($exist), 
           ]);
       }else{
           return $this->redirect(['create']);
       }
    }

    /**
     * Creates a new Perfil model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Perfil();

       $model->user_id = \yii::$app->user->identity->id;
       
      if($exist = RegistrosHelpers::userTiene('perfil')){
          return $this->render('view',[
             'model' => $this->findModel($exist),
          ]);
      }elseif($model->load(Yii::$app->request->post()) && $model->save()){
          return $this->redirect(['view']);
      }else{
          return $this->render('create',[
             'model' => $model, 
          ]);
      }
    }

    /**
     * Updates an existing Perfil model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if($model = Perfil::find()->where(['user_id' => Yii::$app->user->identity->id])->one()){
            if($model->load(Yii::$app->request->post()) && $model->save()){
                return $this->redirect(['view']);
            }else{
                return $this->render('update',[
                    'model'=> $model,
                ]);
            }
        }else{
            throw new NotFoundHttpException('No existe el perfil.');
        }
    }

    /**
     * Deletes an existing Perfil model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
       $model = Perfil::find()->where(['user_id'=> Yii::$app->user->identity->id])->one();
       
       $this->findModel($model->id)->delete();
       
       return $this->redirect(['site/index']);
    }

    /**
     * Finds the Perfil model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Perfil the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Perfil::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
