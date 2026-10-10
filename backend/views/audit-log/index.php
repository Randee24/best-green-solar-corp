<?php

use backend\models\AuditLogs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\AuditLogSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Audit Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audit-logs-index">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">Manage Audit Logs.</p>
        </div>
      
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel, 
                    
                    'tableOptions' => [
                        'class' => 'table table-hover table-striped align-middle mb-0'
                    ],
                    
                    'layout' => "{items}\n<div class='d-flex justify-content-between align-items-center p-3 border-top bg-light small text-muted'>{summary}{pager}</div>",
                    
                    'pager' => [
                        'class' => \yii\bootstrap5\LinkPager::class,
                        'options' => ['class' => 'pagination pagination-sm m-0'],
                        'firstPageLabel' => 'First',
                        'lastPageLabel' => 'Last',
                    ],
                    
                    'columns' => [
                        
                        [
                            'attribute' => 'id',
                            'headerOptions' => ['class' => 'bg-light text-muted small fw-bold', 'style' => 'width: 70px;'],
                            'contentOptions' => ['class' => 'text-muted small'],
                        ],

                        [
                            'attribute' => 'user_id',
                            'headerOptions' => ['class' => 'bg-light text-muted small fw-bold'],
                            'contentOptions' => ['class' => 'fw-semibold text-dark'],
                        ],
						[
                            'attribute' => 'table_name',
                            'headerOptions' => ['class' => 'bg-light text-muted small fw-bold'],
                            'contentOptions' => ['class' => 'fw-semibold text-dark'],
                        ],
						[
                            'attribute' => 'record_id',
                            'headerOptions' => ['class' => 'bg-light text-muted small fw-bold'],
                            'contentOptions' => ['class' => 'fw-semibold text-dark'],
                        ],
						[
                            'attribute' => 'action',
                            'headerOptions' => ['class' => 'bg-light text-muted small fw-bold'],
                            'contentOptions' => ['class' => 'fw-semibold text-dark'],
                        ],
                        [
                            'class' => ActionColumn::class,
                            'header' => 'Actions',
                            'headerOptions' => ['class' => 'bg-light text-muted small fw-bold text-center', 'style' => 'width: 110px;'],
                            'contentOptions' => ['class' => 'text-center'],
                        ],
                    ],
                ]); ?>

            </div>
        </div>
    </div>
</div>
