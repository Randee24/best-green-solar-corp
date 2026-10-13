<?php

use backend\models\Pages;
use yii\grid\GridView;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\search\PagesSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Pages Management';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pages-index">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>
                <?= Html::encode($this->title) ?>
            </h3>
            <div class="text-muted small">Manage pages used by roles and departments.</div>
        </div>

        <?= Html::a(
            '<i class="bi bi-plus-circle me-1"></i> Add Page',
            ['create'],
            ['class' => 'btn btn-primary']
        ) ?>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Page List</h5>
            <span class="badge bg-secondary"><?= number_format($dataProvider->getTotalCount()) ?> Pages</span>
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
					<div class="card-header bg-light p-0" id="pageSearchHeading">
						<button class="btn w-100 text-start border-0 rounded-0 px-3 py-3 d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#pageSearchCollapse" aria-expanded="<?= $hasFilters ? 'true' : 'false' ?>" aria-controls="pageSearchCollapse">
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

					<div class="collapse <?= $hasFilters ? 'show' : '' ?>" id="pageSearchCollapse">
						<div class="card-body border-top">
							<?= $this->render('_search',[
								'model' => $searchModel,
							]) ?>
						</div>
					</div>
				</div>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{items}\n<div class=\"d-flex flex-wrap justify-content-between align-items-center gap-2 p-3\">{summary}{pager}</div>",
                    'summaryOptions' => ['class' => 'small text-muted'],
                    'pager' => [
                        'class' => yii\bootstrap5\LinkPager::class,
                    ],
                    'tableOptions' => [
                        'class' => 'table table-hover table-striped align-middle mb-0',
                        'style' => 'min-width: 780px;',
                    ],
                    'headerRowOptions' => ['class' => 'table-light'],
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'headerOptions' => ['style' => 'width: 75px;'],
                            'contentOptions' => ['class' => 'text-muted'],
                        ],
                        [
                            'attribute' => 'name',
                            'format' => 'raw',
                            'value' => function ($model)
                            {
                                return Html::a(
                                    Html::encode($model->name),
                                    ['view','id' => $model->id],
                                    ['class' => 'fw-semibold text-decoration-none']
                                );
                            },
                        ],
                        [
                            'attribute' => 'model',
                            'format' => 'raw',
                            'value' => function ($model)
                            {
                                return Html::tag('code',Html::encode($model->model));
                            },
                        ],
                        [
                            'attribute' => 'status',
                            'filter' => Pages::getStatusOptions(),
                            'format' => 'raw',
                            'headerOptions' => ['style' => 'width: 150px;'],
                            'value' => function ($model)
                            {
                                $color = (int) $model->status === Pages::STATUS_ACTIVE ? 'success' : 'secondary';

                                return Html::tag(
                                    'span',
                                    Html::encode($model->getStatusLabel()),
                                    ['class' => 'badge bg-' . $color]
                                );
                            },
                        ],
                        [
                            'attribute' => 'created_at',
                            'format' => ['datetime','php:M d, Y h:i A'],
                            'filter' => false,
                            'contentOptions' => ['class' => 'text-nowrap'],
                        ],
                        [
                            'class' => yii\grid\ActionColumn::class,
                            'header' => 'Actions',
                            'template' => '{view} {update} ',
                            'headerOptions' => ['class' => 'text-end','style' => 'width: 160px;'],
                            'contentOptions' => ['class' => 'text-end text-nowrap'],
                            'buttons' => [
                                'view' => function ($url)
                                {
                                    return Html::a('<i class="bi bi-eye"></i>',$url,[
                                        'class' => 'btn btn-sm btn-outline-primary me-1',
                                        'title' => 'View',
                                        'aria-label' => 'View',
                                    ]);
                                },
                                'update' => function ($url)
                                {
                                    return Html::a('<i class="bi bi-pencil"></i>',$url,[
                                        'class' => 'btn btn-sm btn-outline-secondary me-1',
                                        'title' => 'Edit',
                                        'aria-label' => 'Edit',
                                    ]);
                                },
                                
                            ],
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
