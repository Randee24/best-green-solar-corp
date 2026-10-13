<?php

use backend\models\Pages;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var backend\models\Pages $model */
?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">
            <i class="bi bi-file-earmark-text text-primary me-2"></i>
            Page Information
        </h5>
    </div>

    <?php $form = ActiveForm::begin(); ?>

    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <?= $form->field($model,'name')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'e.g. Inventory IN',
                    'class' => 'form-control',
                ]) ?>
            </div>

            <div class="col-12 col-md-6">
                <?= $form->field($model,'model')->textInput([
                    'maxlength' => true,
                    'placeholder' => 'e.g. InventoryIns',
                    'class' => 'form-control',
                ])->hint('Unique identifier used for role and department permissions.') ?>
            </div>

            <div class="col-12 col-md-6">
                <?= $form->field($model,'status')->dropDownList(
                    Pages::getStatusOptions(),
                    ['class' => 'form-select']
                ) ?>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white py-3">
        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <?= Html::a(
                '<i class="bi bi-x-circle me-1"></i> Cancel',
                $model->isNewRecord ? ['index'] : ['view','id' => $model->id],
                ['class' => 'btn btn-outline-secondary']
            ) ?>
            <?= Html::submitButton(
                '<i class="bi bi-check-circle me-1"></i> ' . ($model->isNewRecord ? 'Create Page' : 'Save Changes'),
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
