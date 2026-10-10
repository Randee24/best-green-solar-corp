<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryInItems $model */

$this->title = 'Create Inventory In Items';
$this->params['breadcrumbs'][] = ['label' => 'Inventory In Items', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-in-items-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
