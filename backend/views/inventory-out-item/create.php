<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryOutItems $model */

$this->title = 'Create Inventory Out Items';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Out Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-out-items-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
