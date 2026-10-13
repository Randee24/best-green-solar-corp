<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Pages $model */

$this->title = 'Update Page';
$this->params['breadcrumbs'][] = ['label' => 'Pages Management','url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name,'url' => ['view','id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pages-update">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-pencil-square text-primary me-2"></i>
                <?= Html::encode($this->title) ?>
            </h3>
            <div class="text-muted small">
                Editing <?= Html::encode($model->name) ?>
            </div>
        </div>
        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i> Back',
            ['view','id' => $model->id],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
    </div>

    <?= $this->render('_form',['model' => $model]) ?>
</div>
