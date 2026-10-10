<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryInChanges $model */

$this->title = 'Create Inventory In Changes';
$this->params['breadcrumbs'][] = ['label' => 'Inventory In Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-in-changes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
