<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Suppliers $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="suppliers-form">

    <?php $form = ActiveForm::begin([
        'options' => ['class' => 'needs-validation'],
        'fieldConfig' => [
            'options' => ['class' => 'mb-3'],
            'labelOptions' => ['class' => 'form-label fw-semibold'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">

            <small class="text-muted">
                Enter the supplier details.
            </small>
        </div>

        <div class="card-body p-4">

            <h6 class="text-primary mb-3">
                <i class="bi bi-info-circle me-2"></i>Supplier Information
            </h6>

            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Enter supplier name',
                    ]) ?>
                </div>

            </div>

           

        </div>

        <div class="card-footer bg-light d-flex justify-content-end gap-2 py-3">
            <?= Html::a(
                '<i class="bi bi-arrow-left me-1"></i> Cancel',
                ['index'],
                ['class' => 'btn btn-outline-secondary']
            ) ?>

            <?= Html::submitButton(
                '<i class="bi bi-check-circle me-1"></i> ' .
                ($model->isNewRecord ? 'Create Supplier' : 'Save Changes'),
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

