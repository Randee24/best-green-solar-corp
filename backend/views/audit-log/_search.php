<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\AuditLogSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="audit-logs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'table_name') ?>

    <?= $form->field($model, 'record_id') ?>

    <?= $form->field($model, 'action') ?>

    <?php // echo $form->field($model, 'old_values') ?>

    <?php // echo $form->field($model, 'new_values') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
