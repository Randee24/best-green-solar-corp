<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProductPriceChanges $model */

$this->title = 'Create Product Price Changes';
$this->params['breadcrumbs'][] = ['label' => 'Product Price Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-price-changes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
