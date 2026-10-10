<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\InventoryPeriods $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="inventory-periods-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'year')->textInput() ?>

    <?= $form->field($model, 'month')->textInput() ?>

    <?= $form->field($model, 'status')->dropDownList([ 'open' => 'Open', 'closed' => 'Closed', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'closed_by')->textInput() ?>

    <?= $form->field($model, 'closed_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
