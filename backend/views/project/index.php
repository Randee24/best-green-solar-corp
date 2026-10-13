
<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\ProjectSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="project-index">

    <?= $this->render('//layouts/_flash') ?>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">
                <i class="bi bi-kanban me-2 text-primary"></i>
                Projects
            </h5>

            <?= Html::a(
                '<i class="bi bi-plus-lg me-1"></i> Add Project',
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
					<div class="card-header bg-light p-0" id="projectSearchHeading">
						<button class="btn w-100 text-start border-0 rounded-0 px-3 py-3 d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#projectSearchCollapse" aria-expanded="<?= $hasFilters ? 'true' : 'false' ?>" aria-controls="projectSearchCollapse">
							<span class="fw-semibold">
								<i class="bi bi-funnel me-2 text-primary"></i>
								Search Filters

								<?php if ($hasFilters): ?>
									<span class="badge bg-primary ms-2">Filters Applied</span>
								<?php endif; ?>
							</span>

							<i class="bi bi-chevron-down text-secondary"></i>
						</button>
					</div>

					<div class="collapse <?= $hasFilters ? 'show' : '' ?>" id="projectSearchCollapse">
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
                            'attribute' => 'created_at',
                            'format' => ['datetime','php:M d, Y h:i A'],
                            'filter' => false,
                            'headerOptions' => ['style' => 'width: 190px;'],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => 'Actions',
                            'headerOptions' => [
                                'class' => 'text-center',
                                'style' => 'width: 140px;',
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
                                            'class' => 'btn btn-sm btn-outline-secondary me-1',
                                            'title' => 'Update',
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
