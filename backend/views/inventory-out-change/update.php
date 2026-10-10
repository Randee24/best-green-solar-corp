<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryOutChanges $model */

$this->title = 'Update Inventory Out Changes: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inventory Out Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inventory-out-changes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
