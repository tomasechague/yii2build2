<?php

namespace common\models;

use common\models\ValorHelpers;
use yii;
use yii\web\Controller;
use yii\helpers\Url;


class PermisosHelpers{
    
    public static function requerirUpgrade($tipo_usuario_nombre){
        if(!ValorHelpers::tipoUsusarioCoincide($tipo_usuario_nombre)){
            return Yii::$app->getResponse()->redirect(Url::to['upgrade/index']);
        }
    }
    
    public static function requerirEstado($estado_nombre){
        return ValorHelpers::estadoCoincide($estado_nombre);
    }
    
    public static function requerirMinimoRol($rol_nombre,$userId = null){
        if(ValorHelpers::esRolNombreValido($rol_nombre)){
            if($userId == null){
                $userRolValor = ValorHelpers::getUsersRolValor();
            }else{
                $userRolValor = ValorHelpers::getUsersRolValor($userId);
            }
            return $userRolValor >= ValorHelpers::getRolValor($rol_nombre) ? true : false;
        }else{
            return false;
        }
    }
    
   public static function userDebeSerPropietario($model_nombre, $model_id){
       $conexion = \Yii::$app->db;
       $userid = Yii::$app->user->identity->id;
       $sql = "SELECT id FROM $model_nombre WHERE user_id=:userid AND id=:model_id";
       $comando = $conexion->createCommand($sql);
       $comando->bindValue(":userid", $userid);
       $comando->bindValue(":model_id", $model_id);
       if($result = $comando->queryOne()){
           return true;
       }else{
           return false;
       }
   }
}