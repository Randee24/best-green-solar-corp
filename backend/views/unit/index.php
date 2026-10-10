
<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Units';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="unit-index">

    <?= $this->render('//layouts/_flash') ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">
                <i class="bi bi-rulers text-primary me-2"></i>
                Units
            </h5>

            <?= Html::a(
                '<i class="bi bi-plus-lg me-1"></i> Add Unit',
                ['create'],
                ['class' => 'btn btn-primary btn-sm']
            ) ?>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => [
                        'class' => 'table table-hover table-striped align-middle mb-0',
                    ],
                    'summaryOptions' => [
                        'class' => 'text-muted small px-3 py-2',
                    ],
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'headerOptions' => ['style' => 'width: 90px;'],
                        ],
                        [
                            'attribute' => 'name',
                            'format' => 'raw',
                            'value' => function($model)
                            {
                                return Html::a(
                                    Html::encode($model->name),
                                    ['view','id' => $model->id],
                                    ['class' => 'fw-semibold text-decoration-none']
                                );
                            },
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => 'Actions',
                            'headerOptions' => [
                                'class' => 'text-center',
                                'style' => 'width: 110px;',
                            ],
                            'contentOptions' => ['class' => 'text-center'],
                            'template' => '{view} {update}',
                            'buttons' => [
                                'view' => function($url,$model)
                                {
                                    return Html::a(
                                        '<i class="bi bi-eye"></i>',
                                        ['view','id' => $model->id],
                                        [
                                            'class' => 'btn btn-sm btn-outline-primary me-1',
                                            'title' => 'View',
                                        ]
                                    );
                                },
                                'update' => function($url,$model)
                                {
                                    return Html::a(
                                        '<i class="bi bi-pencil-square"></i>',
                                        ['update','id' => $model->id],
                                        [
                                            'class' => 'btn btn-sm btn-outline-secondary',
                                            'title' => 'Edit',
                                        ]
                                    );
                                },
                            ],
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
