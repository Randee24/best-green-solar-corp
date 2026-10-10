<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SupplierProducts $model */

$this->title = 'Update Supplier Products: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Supplier Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="supplier-products-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
