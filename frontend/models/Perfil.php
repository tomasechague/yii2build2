<?php

namespace frontend\models;

use Yii;

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
        ];
    }
}
