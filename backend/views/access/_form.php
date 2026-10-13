<?php
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var backend\models\Accesses $model */
/** @var array $roleOptions [id => name] */
/** @var array $departmentOptions [id => name] */
/** @var array $pageOptions [id => name] */
?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="bi bi-shield-lock text-primary me-2"></i>Access Information</h5>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <?= $form->field($model,'role_id')->dropDownList($roleOptions,['prompt'=>'Select Role','class'=>'form-select']) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model,'department_id')->dropDownList($departmentOptions,['prompt'=>'Select Department','class'=>'form-select']) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model,'page_id')->dropDownList($pageOptions,['prompt'=>'Select Page','class'=>'form-select']) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model,'status')->dropDownList([1=>'Active',0=>'Inactive'],['class'=>'form-select']) ?>
            </div>
        </div>
    </div>
    <div class="card-footer bg-white py-3 d-flex justify-content-end gap-2">
        <?= Html::a('<i class="bi bi-x-circle me-1"></i> Cancel',$model->isNewRecord ? ['index'] : ['view','id'=>$model->id],['class'=>'btn btn-outline-secondary']) ?>
        <?= Html::submitButton('<i class="bi bi-check-circle me-1"></i> ' . ($model->isNewRecord ? 'Create Access' : 'Save Changes'),['class'=>'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
