<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryOutItems $model */

$this->title = 'Update Inventory Out Items: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inventory Out Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inventory-out-items-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
