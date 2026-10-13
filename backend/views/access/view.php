<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var backend\models\Accesses $model */
/** @var array $roleOptions */
/** @var array $departmentOptions */
/** @var array $pageOptions */
$this->title = 'Access #' . $model->id;
$this->params['breadcrumbs'][] = ['label'=>'Access Management','url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;
$isActive = (int) $model->status === 1;
?>
<div class="access-view">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h3 class="mb-1"><i class="bi bi-shield-lock text-primary me-2"></i><?= Html::encode($this->title) ?>
                <span class="badge bg-<?= $isActive ? 'success' : 'secondary' ?> fs-6 align-middle ms-2"><?= $isActive ? 'Active' : 'Inactive' ?></span>
            </h3>
            <div class="small text-muted">Page permission details</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Back',['index'],['class'=>'btn btn-outline-secondary']) ?>
            <?= Html::a('<i class="bi bi-pencil me-1"></i> Edit',['update','id'=>$model->id],['class'=>'btn btn-primary']) ?>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3"><h5 class="mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Access Information</h5></div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-12 col-md-6"><div class="small text-muted mb-1">Role</div><div class="fw-semibold"><?= Html::encode($roleOptions[$model->role_id] ?? 'Role #' . $model->role_id) ?></div></div>
                <div class="col-12 col-md-6"><div class="small text-muted mb-1">Department</div><div class="fw-semibold"><?= Html::encode($departmentOptions[$model->department_id] ?? 'Department #' . $model->department_id) ?></div></div>
                <div class="col-12 col-md-6"><div class="small text-muted mb-1">Page</div><div class="fw-semibold"><?= Html::encode($pageOptions[$model->page_id] ?? 'Page #' . $model->page_id) ?></div></div>
                <div class="col-12 col-md-6"><div class="small text-muted mb-1">Status</div><span class="badge bg-<?= $isActive ? 'success' : 'secondary' ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span></div>
                <div class="col-12 col-md-6"><div class="small text-muted mb-1">Date Created</div><div class="fw-semibold"><?= Yii::$app->formatter->asDatetime($model->created_at,'php:M d, Y h:i A') ?></div></div>
            </div>
        </div>
    </div>
</div>
