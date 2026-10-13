
<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var backend\models\search\InventoryOutsSearch $model */
/** @var array $periodOptions */

?>

<div class="inventory-outs-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row g-3 align-items-end">
        <div class="col-md-2">
            <?= $form->field($model,'id')->textInput([
                'class' => 'form-control',
                'type' => 'number',
                'min' => 1,
                'placeholder' => 'Reference ID',
            ])->label('Reference') ?>
        </div>
		<div class="col-md-2">
            <?= $form->field($model,'transaction_date')->input('date',[
                'class' => 'form-control',
            ])->label('Transaction Date') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model,'project_name')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Search Project',
            ])->label('Project') ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model,'inventory_period_id')->dropDownList($periodOptions,[
                'prompt' => 'All Periods',
                'class' => 'form-select',
            ])->label('Inventory Period') ?>
        </div>

        

        <div class="col-md-2">
            <?= $form->field($model,'status')->dropDownList([
                'draft' => 'Draft',
                'posted' => 'Posted',
            ],[
                'prompt' => 'All Status',
                'class' => 'form-select',
            ])->label('Status') ?>
        </div>

        <div class="col-md-2">
            <div class="d-flex gap-2 mb-3">
                <?= Html::submitButton('<i class="bi bi-search"></i> Search',[
                    'class' => 'btn btn-primary',
                ]) ?>

                <?= Html::a('<i class="bi bi-arrow-clockwise"></i> Reset',['index'],[
                    'class' => 'btn btn-outline-secondary',
                    'title' => 'Reset Filters',
                ]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
