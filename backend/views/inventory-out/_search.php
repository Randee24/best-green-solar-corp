<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\InventoryOutSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="inventory-outs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'date_and_time') ?>

    <?= $form->field($model, 'project_id') ?>

    <?= $form->field($model, 'transaction_date') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'inventory_period_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
