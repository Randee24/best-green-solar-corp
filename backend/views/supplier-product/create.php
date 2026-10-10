<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SupplierProducts $model */

$this->title = 'Create Supplier Products';
$this->params['breadcrumbs'][] = ['label' => 'Supplier Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="supplier-products-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
