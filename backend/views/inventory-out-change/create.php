<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryOutChanges $model */

$this->title = 'Create Inventory Out Changes';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Out Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-out-changes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
