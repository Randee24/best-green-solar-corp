
<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Product $model */

$this->title = 'Create Product';

$this->params['breadcrumbs'][] = [
    'label' => 'Products',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="product-create">
<?= $this->render('//layouts/_flash') ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-plus-circle text-primary me-2"></i>
                Create Product
            </h3>

            <p class="text-muted mb-0">
                Add a new product to your inventory catalog.
            </p>
        </div>

        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i> Back to Products',
            ['index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>

    </div>

    <?= $this->render('_form',[
        'model' => $model,
    ]) ?>

</div>
