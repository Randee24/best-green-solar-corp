<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Suppliers $model */

$this->title = 'Update Suppliers: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="suppliers-update">

    <h1><i class="bi bi-truck me-2"></i><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
