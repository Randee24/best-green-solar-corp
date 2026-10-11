<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryIns $model */

$this->title = 'Update Inventory Ins: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inventory Ins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="inventory-ins-update">


    <?= $this->render('_form',[
		'model' => $model,
		'products' => $products,
		'suppliers' => $suppliers,
		'items' => $items,
	]) ?>

</div>
