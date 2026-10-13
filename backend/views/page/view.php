<?php

use backend\models\Pages;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Pages $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Pages Management','url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$isActive = (int) $model->status === Pages::STATUS_ACTIVE;
?>

<div class="pages-view">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                <?= Html::encode($model->name) ?>
                <span class="badge bg-<?= $isActive ? 'success' : 'secondary' ?> fs-6 align-middle ms-2">
                    <?= Html::encode($model->getStatusLabel()) ?>
                </span>
            </h3>
            <div class="text-muted small">Page #<?= Html::encode($model->id) ?></div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <?= Html::a(
                '<i class="bi bi-arrow-left me-1"></i> Back',
                ['index'],
                ['class' => 'btn btn-outline-secondary']
            ) ?>
            <?= Html::a(
                '<i class="bi bi-pencil me-1"></i> Edit',
                ['update','id' => $model->id],
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-info-circle text-primary me-2"></i>
                Page Information
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="text-muted small mb-1">Page Name</div>
                    <div class="fw-semibold"><?= Html::encode($model->name) ?></div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="text-muted small mb-1">Model Identifier</div>
                    <code><?= Html::encode($model->model) ?></code>
                </div>
                <div class="col-12 col-md-6">
                    <div class="text-muted small mb-1">Status</div>
                    <span class="badge bg-<?= $isActive ? 'success' : 'secondary' ?>">
                        <?= Html::encode($model->getStatusLabel()) ?>
                    </span>
                </div>
                <div class="col-12 col-md-6">
                    <div class="text-muted small mb-1">Date Created</div>
                    <div class="fw-semibold">
                        <?= Yii::$app->formatter->asDatetime($model->created_at,'php:M d, Y h:i A') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
