<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model frontend\models\Perfil */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="perfil-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'apellido')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'fecha_nacimiento')
             ->widget(DatePicker::className(),[
                     'dateFormat'=>'yyy-MM-dd',
                     'clientOptions'=>[
                         'yearRange'=>'-115:+0',
                         'changeYear'=>true]
                     ])?>

    <?= $form->field($model, 'genero_id')->dropDownList($model->generoLista,['prompt'=>'Por favor seleccione uno']); ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
