
<?php

use yii\helpers\Html;

$this->title = 'Create Product Type';
$this->params['breadcrumbs'][] = [
    'label' => 'Product Types',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="product-type-create">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-plus-square text-primary me-2"></i>
                Create Product Type
            </h3>
            <p class="text-muted mb-0">
                Add a new product type to the system.
            </p>
        </div>

        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i> Back',
            ['index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
    </div>

    <?= $this->render('_form',[
        'model' => $model,
    ]) ?>

</div>
