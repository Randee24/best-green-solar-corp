<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\InventoryOutChanges $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="inventory-out-changes-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inventory_out_id')->textInput() ?>

    <?= $form->field($model, 'old_value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'new_value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'changed_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
