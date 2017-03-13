<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\User;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

/**
 * This is the model class for table "perfil".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $nombre
 * @property string $apellido
 * @property string $fecha_nacimiento
 * @property string $created_ad
 * @property string $updated_at
 * @property integer $genero_id
 */
class Perfil extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'perfil';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'genero_id'], 'required'],
            [['user_id', 'genero_id'], 'integer'],
            [['nombre', 'apellido'], 'string'],
            [['fecha_nacimiento', 'created_ad', 'updated_at'], 'safe'],
            [['fecha_nacimiento'],'date','format'=>'Y-m-d'],
            [['genero_id'],'in','range'=> array_keys($this->getGeneroLista())],
            
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
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'fecha_nacimiento' => 'Fecha Nacimiento',
            'created_ad' => 'Created Ad',
            'updated_at' => 'Updated At',
            'genero_id' => 'Genero ID',
            'generoNombre'=> Yii::t('app', 'Genero'),
            'userLink'=> Yii::t('app', 'User'),
            'perfilIdLink'=> Yii::t('app', 'Perfil'),
        ];
    }
    
    public function behaviors() {
        return [
          'timestamp'=>[
              'class'=>'yii\behaviors\TimestampBehavior',
              'attributes'=>[
              ActiveRecord::EVENT_BEFORE_INSERT=>['created_ad','updated_at'],
              ActiveRecord::EVENT_BEFORE_UPDATE=>['updated_at'],
              ],
              'value'=>new Expression('NOW()'),
          ]  ,
        ];
    }
    
    public function getGenero(){
        return $this->hasOne(Genero::className(),['id'=>'genero_id']);
    }
    
    public function getGeneroNombre(){
        return $this->genero->genero_nombre;
    }
    
    public static function getGeneroLista(){
       $droptions = Genero::find()->asArray()->all();
       return ArrayHelper::map($droptions, 'id', 'genero_nombre');
    }
    
    public function getUser(){
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }
    
    public function getUsername(){
        return $this->user->username;
    }

    public function getUserId(){
        return $this->user ? $this->user->id : 'none';
    }
    
    public function getUserLink(){
        $url = Url::to(['user/view','id'=> $this->UserId]);
        $opciones = [];
        return Html::a($this->getUsername(),$url,$opciones);
    }
    
    public function getPerfilIdLink(){
        $url = Url::to(['perfil/update','id'=> $this->id]);
        $opciones = [];
        return Html::a($this->id,$url,$opciones);
    }
}
