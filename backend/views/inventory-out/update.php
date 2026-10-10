<?php

use yii\helpers\Html;

$this->title = 'Update Inventory OUT #' . $model->id;

$this->params['breadcrumbs'][] = [
    'label' => 'Inventory OUT',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="inventory-out-update">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-pencil-square text-primary me-2"></i>
                Update Inventory OUT
            </h3>
            <p class="text-muted mb-0">
                Transaction #<?= Html::encode($model->id) ?>
            </p>
        </div>

        <?= Html::a('Back',['view','id' => $model->id],[
            'class' => 'btn btn-outline-secondary',
        ]) ?>
    </div>

    <?= $this->render('//layouts/_flash') ?>

    <?= $this->render('_form',[
        'model' => $model,
        'items' => $items,
    ]) ?>

</div>