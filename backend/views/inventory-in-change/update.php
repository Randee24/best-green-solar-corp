<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryInChanges $model */

$this->title = 'Update Inventory In Changes: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inventory In Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inventory-in-changes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
