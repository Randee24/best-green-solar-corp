
<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Product Type';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="product-type-index">

    <?= $this->render('//layouts/_flash') ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">
                <i class="bi bi-rulers text-primary me-2"></i>
                Product Types
            </h5>

            <?= Html::a(
                '<i class="bi bi-plus-lg me-1"></i> Add Product Type',
                ['create'],
                ['class' => 'btn btn-primary btn-sm']
            ) ?>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
				
				<?php

				$filterValues = Yii::$app->request->get($searchModel->formName(),[]);

				$hasFilters = !empty(array_filter($filterValues,function ($value)
				{
					return $value !== '' && $value !== null;
				}));

				?>

				<div class="card border shadow-sm mb-3 overflow-hidden">
					<div class="card-header bg-light p-0" id="productTypeSearchHeading">
						<button class="btn w-100 text-start border-0 rounded-0 px-3 py-3 d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#productTypeSearchCollapse" aria-expanded="<?= $hasFilters ? 'true' : 'false' ?>" aria-controls="productTypeSearchCollapse">
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

					<div class="collapse <?= $hasFilters ? 'show' : '' ?>" id="productTypeSearchCollapse">
						<div class="card-body border-top">
							<?= $this->render('_search',[
								'model' => $searchModel,
							]) ?>
						</div>
					</div>
				</div>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
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
