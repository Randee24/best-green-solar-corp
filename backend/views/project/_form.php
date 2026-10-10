
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\Projects $model */

$isNew = $model->isNewRecord;
?>

<div class="project-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-pencil-square me-2 text-primary"></i>
                <?= $isNew ? 'New Project Details' : 'Edit Project Details' ?>
            </h5>
        </div>

        <div class="card-body p-4">
            <?= $form->errorSummary($model,[
                'class' => 'alert alert-danger',
            ]) ?>

            <?= $form->field($model,'name')->textInput([
                'maxlength' => true,
                'class' => 'form-control',
                'placeholder' => 'Enter project name',
                'autofocus' => true,
            ]) ?>
        </div>

        <div class="card-footer bg-white d-flex justify-content-end gap-2 py-3">
            <?= Html::a(
                'Cancel',
                $isNew ? ['index'] : ['view','id' => $model->id],
                ['class' => 'btn btn-outline-secondary']
            ) ?>

            <?= Html::submitButton(
                $isNew
                    ? '<i class="bi bi-plus-circle me-1"></i> Create Project'
                    : '<i class="bi bi-check-circle me-1"></i> Save Changes',
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
