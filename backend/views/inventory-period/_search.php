
<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var backend\models\search\InventoryPeriodsSearch $model */

$months = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December',
];

$years = array_combine(
    range((int) date('Y') + 1,2020),
    range((int) date('Y') + 1,2020)
);

?>

<div class="inventory-periods-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <?= $form->field($model,'month')->dropDownList($months,[
                'prompt' => 'All Months',
                'class' => 'form-select',
            ])->label('Month') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model,'year')->dropDownList($years,[
                'prompt' => 'All Years',
                'class' => 'form-select',
            ])->label('Year') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model,'status')->dropDownList([
                'open' => 'Open',
                'closed' => 'Closed',
            ],[
                'prompt' => 'All Status',
                'class' => 'form-select',
            ])->label('Status') ?>
        </div>

        <div class="col-md-3">
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
