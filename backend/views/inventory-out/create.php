<?php

use yii\helpers\Html;

$this->title = 'Create Inventory OUT';

$this->params['breadcrumbs'][] = [
    'label' => 'Inventory OUT',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="inventory-out-create">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-box-arrow-up text-primary me-2"></i>
                Create Inventory OUT
            </h3>
            <p class="text-muted mb-0">Release products to a project.</p>
        </div>

        <?= Html::a('Back',['index'],['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?= $this->render('//layouts/_flash') ?>

    <?= $this->render('_form',[
        'model' => $model,
        'items' => $items,
    ]) ?>

</div>