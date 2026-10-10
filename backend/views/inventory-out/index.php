<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Inventory OUT';

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="inventory-out-index">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-box-arrow-up text-primary me-2"></i>
                Inventory OUT
            </h3>
            <p class="text-muted mb-0">Manage outgoing inventory transactions.</p>
        </div>

        <?= Html::a(
            '<i class="bi bi-plus-circle me-1"></i> Create Inventory OUT',
            ['create'],
            ['class' => 'btn btn-primary']
        ) ?>

    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Inventory OUT Transactions</h5>
        </div>

        <div class="card-body">

            <?php Pjax::begin(['id' => 'inventory-out-grid']); ?>

            <div class="table-responsive">

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => [
                        'class' => 'table table-hover table-bordered align-middle',
                    ],
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'label' => 'Reference',
                            'format' => 'raw',
                            'value' => function($model)
                            {
                                return Html::a(
                                    'OUT-' . str_pad($model->id,5,'0',STR_PAD_LEFT),
                                    ['view','id' => $model->id],
                                    ['data-pjax' => '0']
                                );
                            },
                        ],
                        [
                            'attribute' => 'transaction_date',
                            'format' => ['date','php:M d, Y'],
                        ],
                        [
                            'attribute' => 'project_name',
                            'label' => 'Project',
                            'value' => function($model)
                            {
                                return $model->project->name ?? 'N/A';
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'filter' => [
                                'draft' => 'Draft',
                                'posted' => 'Posted',
                                'reversed' => 'Reversed',
                            ],
                            'value' => function($model)
                            {
                                $colors = [
                                    'draft' => 'warning',
                                    'posted' => 'success',
                                    'reversed' => 'secondary',
                                ];

                                $color = $colors[$model->status] ?? 'secondary';

                                return '<span class="badge bg-' . $color . '">'
                                    . Html::encode(ucfirst($model->status))
                                    . '</span>';
                            },
                        ],
                        [
                            'attribute' => 'date_and_time',
                            'label' => 'Date & Time',
                            'format' => ['datetime','php:M d, Y h:i A'],
                            'filter' => false,
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{view} {update}',
                            'buttons' => [
                                'view' => function($url,$model)
                                {
                                    return Html::a(
                                        '<i class="bi bi-eye"></i>',
                                        ['view','id' => $model->id],
                                        [
                                            'class' => 'btn btn-sm btn-outline-primary me-1',
                                            'data-pjax' => '0',
                                        ]
                                    );
                                },
                                'update' => function($url,$model)
                                {
                                    if ($model->status !== 'draft')
                                    {
                                        return '';
                                    }

                                    return Html::a(
                                        '<i class="bi bi-pencil"></i>',
                                        ['update','id' => $model->id],
                                        [
                                            'class' => 'btn btn-sm btn-outline-secondary',
                                            'data-pjax' => '0',
                                        ]
                                    );
                                },
                            ],
                        ],
                    ],
                ]) ?>

            </div>

            <?php Pjax::end(); ?>

        </div>
    </div>

</div>