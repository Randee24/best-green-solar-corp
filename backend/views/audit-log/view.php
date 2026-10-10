<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\AuditLogs $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Audit Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="audit-logs-view">

    <!-- Top Action Toolbar Buttons -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Information related to Audit Logs.</p>
        </div>
        <div>
            <?= Html::a('<i class="bi bi-arrow-left"></i> Back', ['index'], ['class' => 'btn btn-light btn-sm border px-3 me-2']) ?>
        </div>
    </div>

    <!-- Bootstrap Card Wrapper -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-light border-bottom p-3">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="bi bi-box-seam me-2 text-primary"></i> Audit Log Details
            </h5>
        </div>
        <div class="card-body p-0">
            
            <?= DetailView::widget([
                'model' => $model,
                'options' => [
                    'class' => 'table table-hover table-striped align-middle mb-0'
                ],
                'attributes' => [
                   
                    [
                        'attribute' => 'id',
                        'captionOptions' => ['class' => 'text-muted small fw-bold'],
                        'contentOptions' => ['class' => 'fw-semibold text-dark'],
                    ],
					[
                        'attribute' => 'user_id',
                        'captionOptions' => ['class' => 'text-muted small fw-bold'],
                        'contentOptions' => ['class' => 'fw-semibold text-dark'],
                    ],
					[
                        'attribute' => 'table_name',
                        'captionOptions' => ['class' => 'text-muted small fw-bold'],
                        'contentOptions' => ['class' => 'fw-semibold text-dark'],
                    ],
					[
                        'attribute' => 'Record_id',
                        'captionOptions' => ['class' => 'text-muted small fw-bold'],
                        'contentOptions' => ['class' => 'fw-semibold text-dark'],
                    ],
					[
                        'attribute' => 'Action',
                        'captionOptions' => ['class' => 'text-muted small fw-bold'],
                        'contentOptions' => ['class' => 'fw-semibold text-dark'],
                    ],
                    [
                        'attribute' => 'created_at',
                        'captionOptions' => ['class' => 'text-muted small fw-bold'],
                        'contentOptions' => ['class' => 'text-muted small'],
                        'value' => date('F d, Y h:i A', strtotime($model->created_at)),
                    ],
                ],
            ]) ?>

        </div>
    </div>
</div>
