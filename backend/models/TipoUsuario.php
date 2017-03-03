<?php

namespace backend\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "tipo_usuario".
 *
 * @property integer $id
 * @property string $tipo_usuario_nombre
 * @property integer $tipo_usuario_valor
 */
class TipoUsuario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tipo_usuario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tipo_usuario_nombre', 'tipo_usuario_valor'], 'required'],
            [['tipo_usuario_valor'], 'integer'],
            [['tipo_usuario_nombre'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo_usuario_nombre' => 'Tipo Usuario Nombre',
            'tipo_usuario_valor' => 'Tipo Usuario Valor',
        ];
    }
    
    public function getUsers(){
        return $this->hasMany(User::className(), ['tipo_usuario_id'=>'id']);
    }
}
