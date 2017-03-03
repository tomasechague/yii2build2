<?php

namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use yii\helpers\Security;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\models\Rol;
use backend\models\Estado;
use backend\models\TipoUsuario;
use frontend\models\Perfil;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $rol_id
 * @property integer $estado_id
 * @property integer $tipo_usuario_id
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface {

    const ESTADO_ACTIVO = 1;

    //const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        //return '{{%user}}';
        return 'user';
    }

    /**
     * @inheritdoc
     * Este metodo se disparara cada vez que un registro sea creado o actualizado
     * y pondra la entrada apropiada en la base de datos en el formato DateTime correcto
     */
    public function behaviors() {
        return[
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['update_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];


//        return [
//            TimestampBehavior::className(),
//        ];
    }

    /**
     * @inheritdoc
     * reglas de validacion
     */
    public function rules() {
        return[
            ['estado_id', 'default', 'value' => self::ESTADO_ACTIVO],
            [['estado_id'], 'in', 'range' => array_keys($this->getEstadoLista())],
            ['rol_id', 'default', 'value' => 1],
            [['rol_id'], 'in', 'range' => array_keys($this->getRolLista())],
            ['tipo_usuario_id', 'default', 'value' => 1],
            ['tipo_usuario_id', 'in', 'range' => array_keys($this->getTipoUsuarioLista())],
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique'],
        ];

//        return [
//            ['status', 'default', 'value' => self::STATUS_ACTIVE],
//            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
//        ];
    }

    /* Las etiquetas de los atributos de su modelo */

    public function attributeLabels() {
        return[
            /* Sus otras etiquetas de atributo */
            'rolNombre' => Yii::t('app', 'Rol'),
            'estadoNombre' => Yii::t('app', 'Estado'),
            'perfilId' => Yii::t('app', 'Perfil'),
            'perfilLink' => Yii::t('app', 'Perfil'),
            'userLink' => Yii::t('app', 'User'),
            'username' => Yii::t('app', 'User'),
            'tipoUsuarioNombre' => Yii::t('app', 'Tipo Usuario'),
            'tipoUsuarioId' => Yii::t('app', 'Tipo Usuario'),
            'userIdLink' => Yii::t('app', 'ID'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        return static::findOne(['id' => $id, 'estado_id' => self::ESTADO_ACTIVO]);
        //return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Encuentra usuario por username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        return static::findOne(['username' => $username, 'estado_id' => self::ESTADO_ACTIVO]);
        //return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Encuentra usuario por clave de restablecimiento de password
     *
     * @param string $token clave de restablecimiento de password
     * @return static|null
     */
    public static function findByPasswordResetToken($token) {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
                    'password_reset_token' => $token,
                    'status' => self::ESTADO_ACTIVO,
        ]);
    }

    /**
     * Determina si la clave de restablecimiento de password es valida
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token) {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey() {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Genera hash de password a partir de password y la establece en el modelo
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Genera clave de autentificacion "recuerdame"
     */
    public function generateAuthKey() {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Genera nueva clave de restablecimiento de password
     */
    public function generatePasswordResetToken() {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Remueve clave de restablecimiento de password
     */
    public function removePasswordResetToken() {
        $this->password_reset_token = null;
    }

    public function getPerfil() {
        return $this->hasOne(Perfil::className(), ['user_id' => 'id']);
    }

    /*
     * Relacion get rol
     */

    public function getRol() {
        return $this->hasOne(Rol::className(), ['rol_id' => 'id']);
    }

    /*
     * get rol nombre
     */

    public function getRolNombre() {
        return $this->rol ? $this->rol->rol_nombre : '-Sin rol-';
    }

    /*
     * obtiene lista de roles para lista desplegable
     */

    public static function getRolLista() {
        $dropciones = Rol::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'rol_nombre');
    }

    /*
     * Relacion obtener estado
     */

    public function getEstado() {
        return $this->hasOne(Estado::className(), ['id' => 'estado_id']);
    }

    /*
     * Obtiene el nombre del estado
     */

    public function getEstadoNombre() {
        return $this->estado ? $this->estado->estado_nombre : '-sin estado-';
    }

    /*
     * Obtiene lista desplegable de los estados
     */

    public static function getEstadoLista() {
        $dropciones = Estado::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'estado_nombre');
    }

    public function getTipoUsuario() {
        return $this->hasOne(TipoUsuario::className(), ['id' => 'tipo_usuario_id']);
    }

    /*
     * obtiene el nombre del tipo de usuario
     */

    public function getTipoUsuarioNombre() {
        return $this->tipoUsuario ? $this->tipoUsuario->tipo_usuario_nombre : '-sin tipo usuario-';
    }

    /*
     * Obtiene lista desplegable de los tipos de usuario
     */

    public static function getTipoUsuarioLista() {
        $dropciones = TipoUsuario::find()->asArray()->all();
        return ArrayHelper::map($dropciones, 'id', 'tipo_usuario_nombre');
    }

    /*
     * Obtiene el usuario tipo usuario id
     */

    public function getTipoUsuarioId() {
        return $this->tipoUsuario ? $this->tipoUsuario->id : 'ninguno';
    }

    public function getPerfilId() {
        return $this->perfil ? $this->perfil->id : 'ninguno';
    }

    public function getPerfilLink() {
        $url = Url::to(['perfil/view', 'id' => $this->perfilId]);
        $opciones = [];
        return Html::a($this->perfil ? 'perfil' : 'ninguno', $url, $opciones);
    }

    public function getUserIdLink() {
        $url = Url::to(['user/update', 'id' => $this->id]);
        $opciones = [];
        return Html::a($this->id, $url, $opciones);
    }

    public function getUserLink() {
        $url = Url::to(['user/view', 'id' => $this->id]);
        $opciones = [];
        return Html::a($this->username, $url, $opciones);
    }

}
