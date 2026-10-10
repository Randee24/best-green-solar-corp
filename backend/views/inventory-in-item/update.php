<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryInItems $model */

$this->title = 'Update Inventory In Items: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inventory In Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inventory-in-items-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
