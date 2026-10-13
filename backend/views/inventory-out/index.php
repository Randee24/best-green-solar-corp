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

				<?php

				$filterValues = Yii::$app->request->get($searchModel->formName(),[]);

				$hasFilters = !empty(array_filter($filterValues,function ($value)
				{
					return $value !== '' && $value !== null;
				}));

				?>

				<div class="card border shadow-sm mb-3 overflow-hidden">
					<div class="card-header bg-light p-0" id="inventoryOutSearchHeading">
						<button class="btn w-100 text-start border-0 rounded-0 px-3 py-3 d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#inventoryOutSearchCollapse" aria-expanded="<?= $hasFilters ? 'true' : 'false' ?>" aria-controls="inventoryOutSearchCollapse">
							<span class="fw-semibold">
								<i class="bi bi-funnel me-2 text-primary"></i>
								Search Filter

								<?php if ($hasFilters): ?>
									<span class="badge bg-primary ms-2">Filters Applied</span>
								<?php endif; ?>
							</span>

							<i class="bi bi-chevron-down text-secondary"></i>
						</button>
					</div>

					<div class="collapse <?= $hasFilters ? 'show' : '' ?>" id="inventoryOutSearchCollapse">
						<div class="card-body border-top">
							<?= $this->render('_search',[
								'model' => $searchModel,
								'periodOptions' => $periodOptions,
							]) ?>
						</div>
					</div>
				</div>
		
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
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
							'attribute' => 'period_name',
							'label' => 'Period',
							'format' => 'raw',
							'value' => function($model)
							{
								$period = $model->inventoryPeriod;

								if ($period === null)
								{
									return '<span class="text-muted">N/A</span>';
								}

								$month = date('M',mktime(0,0,0,(int) $period->month,1));

								$badge = $period->status === 'open'
									? 'bg-success'
									: 'bg-secondary';

								return Html::encode($month . ' ' . $period->year)
									. ' <span class="badge ' . $badge . '">'
									. Html::encode(ucfirst($period->status))
									. '</span>';
							},
						],
						[
							'label' => 'Products Count',
							'value' => function($model)
							{
								return count($model->inventoryOutItems);
							},
							'contentOptions' => [
								'class' => 'text-center',
							],
						],
						[
							'label' => 'Total Qty',
							'value' => function($model)
							{
								$quantity = 0;

								foreach ($model->inventoryOutItems as $item)
								{
									$quantity += (float) $item->qty;
								}

								return $quantity;
							},
							'contentOptions' => [
								'class' => 'text-end',
							],
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