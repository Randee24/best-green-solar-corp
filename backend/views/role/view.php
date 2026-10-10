
<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => 'Roles',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="role-view">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-rulers text-primary me-2"></i>
                <?= Html::encode($model->name) ?>
            </h3>
            <p class="text-muted mb-0">
                Role #<?= Html::encode($model->id) ?>
            </p>
        </div>

        <div class="d-flex gap-2">
            <?= Html::a(
                '<i class="bi bi-arrow-left me-1"></i> Back',
                ['index'],
                ['class' => 'btn btn-outline-secondary']
            ) ?>

            <?= Html::a(
                '<i class="bi bi-pencil-square me-1"></i> Edit',
                ['update','id' => $model->id],
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-info-circle text-primary me-2"></i>
                Role Information
            </h5>
        </div>

        <div class="card-body p-0">
            <?= DetailView::widget([
                'model' => $model,
                'options' => [
                    'class' => 'table table-striped align-middle mb-0',
                ],
                'attributes' => [
                    'id',
                    'name',
                ],
            ]) ?>
        </div>
    </div>

</div>
