<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\InventoryInChangeSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="inventory-in-changes-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'inventory_in_id') ?>

    <?= $form->field($model, 'old_value') ?>

    <?= $form->field($model, 'new_value') ?>

    <?= $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'changed_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
