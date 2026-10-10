<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Suppliers $model */

$this->title = 'Create Suppliers';
$this->params['breadcrumbs'][] = ['label' => 'Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="suppliers-create">

    <h1><i class="bi bi-truck me-2"></i><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
