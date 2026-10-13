
<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\SuppliersSearch $model */

?>

<div class="suppliers-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <?= $form->field($model,'name')->textInput([
                'class' => 'form-control',
                'placeholder' => 'Search Supplier Name',
            ])->label('Supplier Name') ?>
        </div>

        <div class="col-md-2">
            <div class="d-flex gap-2 mb-3">
                <?= Html::submitButton('<i class="bi bi-search"></i> Search',[
                    'class' => 'btn btn-primary',
                ]) ?>

                <?= Html::a('<i class="bi bi-arrow-clockwise"></i>',['index'],[
                    'class' => 'btn btn-outline-secondary',
                    'title' => 'Reset Filters',
                ]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
