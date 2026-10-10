<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryPeriods $model */

$this->title = 'Create Inventory Periods';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Periods', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-periods-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
