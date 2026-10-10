
<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Product $model */

$this->title = 'Update Product: ' . $model->name;

$this->params['breadcrumbs'][] = [
    'label' => 'Products',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = [
    'label' => $model->name,
    'url' => ['view','id' => $model->id],
];

$this->params['breadcrumbs'][] = 'Update';

?>

<div class="product-update">
<?= $this->render('//layouts/_flash') ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-pencil-square text-primary me-2"></i>
                Update Product
            </h3>

            <p class="text-muted mb-0">
                Editing:
                <strong><?= Html::encode($model->name) ?></strong>
            </p>
        </div>

        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i> Back to Product',
            ['view','id' => $model->id],
            ['class' => 'btn btn-outline-secondary']
        ) ?>

    </div>

    <?= $this->render('_form',[
        'model' => $model,
    ]) ?>

</div>
