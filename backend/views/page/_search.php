
<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\PagesSearch $model */

?>

<div class="pages-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <?= $form->field($model,'name')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Search Page Name',
            ])->label('Page Name') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model,'model')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Search Model',
            ])->label('Model') ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($model,'status')->dropDownList([
                1 => 'Active',
                0 => 'Inactive',
            ],[
                'prompt' => 'All Status',
                'class' => 'form-select',
            ])->label('Status') ?>
        </div>

        <div class="col-md-4">
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
