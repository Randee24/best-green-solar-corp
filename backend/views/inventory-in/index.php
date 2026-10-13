<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\InventoryIns;

/** @var yii\web\View $this */
/** @var backend\models\InventoryInsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Inventory IN';

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="inventory-in-index">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-box-arrow-in-down text-primary me-2"></i>
                Inventory IN
            </h3>

            <p class="text-muted mb-0">
                Manage incoming inventory receipts and supplier deliveries.
            </p>
        </div>

        <?= Html::a(
            '<i class="bi bi-plus-circle me-1"></i> New Inventory IN',
            ['create'],
            ['class' => 'btn btn-primary']
        ) ?>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2 text-primary"></i>
                Inventory Receipts
            </h5>
        </div>

        <div class="card-body">

            <?php Pjax::begin([
                'id' => 'inventory-in-grid',
                'timeout' => 10000,
                'enablePushState' => true,
            ]); ?>
			
				<?php

				$filterValues = Yii::$app->request->get($searchModel->formName(),[]);

				$hasFilters = !empty(array_filter($filterValues,function ($value)
				{
					return $value !== '' && $value !== null;
				}));

				?>

				<div class="card border shadow-sm mb-3 overflow-hidden">
					<div class="card-header bg-light p-0" id="inventoryInSearchHeading">
						<button class="btn w-100 text-start border-0 rounded-0 px-3 py-3 d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#inventoryInSearchCollapse" aria-expanded="<?= $hasFilters ? 'true' : 'false' ?>" aria-controls="inventoryInSearchCollapse">
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

					<div class="collapse <?= $hasFilters ? 'show' : '' ?>" id="inventoryInSearchCollapse">
						<div class="card-body border-top">
							<?= $this->render('_search',[
								'model' => $searchModel,
								'supplierOptions' => $supplierOptions,
								'periodOptions' => $periodOptions,
								'productOptions' => $productOptions,
							]) ?>
						</div>
					</div>
				</div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => [
                    'class' => 'table table-hover table-bordered align-middle',
                ],
                'summaryOptions' => [
                    'class' => 'text-muted small mb-3',
                ],
                'columns' => [

                    [
                        'attribute' => 'id',
                        'label' => 'Reference',
                        'format' => 'raw',
                        'value' => function($model)
                        {
                            return Html::a(
                                'IN-' . str_pad($model->id,5,'0',STR_PAD_LEFT),
                                ['view','id' => $model->id],
                                ['class' => 'fw-semibold text-decoration-none']
                            );
                        },
                        'contentOptions' => [
                            'style' => 'white-space:nowrap;',
                        ],
                    ],

                    [
                            'attribute' => 'transaction_date',
                            'format' => ['date','php:M d, Y'],
                        ],

                    [
                        'attribute' => 'supplier_name',
                        'label' => 'Supplier',
                        'value' => function($model)
                        {
                            return $model->supplier->name ?? 'N/A';
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
                            return count($model->inventoryInItems);
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

                            foreach ($model->inventoryInItems as $item)
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
                        'label' => 'Total Amount',
                        'format' => 'raw',
                        'value' => function($model)
                        {
                            $total = 0;

                            foreach ($model->inventoryInItems as $item)
                            {
                                $total += (float) $item->total_amount;
                            }

                            return '<span class="fw-semibold">₱'
                                . number_format($total,2)
                                . '</span>';
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
                            $classes = [
                                'draft' => 'bg-secondary',
                                'posted' => 'bg-success',
                                'reversed' => 'bg-danger',
                            ];

                            $class = $classes[$model->status] ?? 'bg-secondary';

                            return '<span class="badge ' . $class . '">'
                                . Html::encode(strtoupper($model->status))
                                . '</span>';
                        },
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Actions',
                        'template' => '{view} {update}',
                        'buttons' => [
                            'view' => function($url,$model)
                            {
                                return Html::a(
                                    '<i class="bi bi-eye"></i>',
                                    ['view','id' => $model->id],
                                    [
                                        'class' => 'btn btn-sm btn-outline-primary me-1',
                                        'title' => 'View Receipt',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                            'update' => function($url,$model)
                            {
                                if ($model->status !== 'draft'
                                    || $model->inventoryPeriod === null
                                    || $model->inventoryPeriod->status !== 'open')
                                {
                                    return '';
                                }

                                return Html::a(
                                    '<i class="bi bi-pencil"></i>',
                                    ['update','id' => $model->id],
                                    [
                                        'class' => 'btn btn-sm btn-outline-secondary',
                                        'title' => 'Edit Receipt',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },
                        ],
                        'contentOptions' => [
                            'style' => 'white-space:nowrap;',
                        ],
                    ],

                ],
            ]) ?>

            <?php Pjax::end(); ?>

        </div>
    </div>

</div>