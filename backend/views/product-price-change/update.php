<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProductPriceChanges $model */

$this->title = 'Update Product Price Changes: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Product Price Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="product-price-changes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
