<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap5\LinkPager;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'Access Management';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="access-index">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h3 class="mb-1"><i class="bi bi-shield-lock text-primary me-2"></i><?= Html::encode($this->title) ?></h3>
            <div class="small text-muted">Manage page access by role and department.</div>
        </div>
        <?= Html::a('<i class="bi bi-plus-circle me-1"></i> Add Access',['create'],['class'=>'btn btn-primary']) ?>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Access List</h5>
            <span class="badge bg-secondary"><?= number_format($dataProvider->getTotalCount()) ?> Records</span>
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
				<div class="card-header bg-light p-0" id="accessSearchHeading">
					<button class="btn w-100 text-start border-0 rounded-0 px-3 py-3 d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#accessSearchCollapse" aria-expanded="<?= $hasFilters ? 'true' : 'false' ?>" aria-controls="accessSearchCollapse">
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

				<div class="collapse <?= $hasFilters ? 'show' : '' ?>" id="accessSearchCollapse">
					<div class="card-body border-top">
						<?= $this->render('_search',[
							'searchModel' => $searchModel,
							'roleOptions' => $roleOptions,
							'departmentOptions' => $departmentOptions,
							'pageOptions' => $pageOptions,
						]) ?>
					</div>
				</div>
			</div>


                <?= GridView::widget([
                    'dataProvider'=>$dataProvider,
                    'layout'=>"{items}\n<div class=\"d-flex flex-wrap justify-content-between align-items-center gap-2 p-3\">{summary}{pager}</div>",
                    'summaryOptions'=>['class'=>'small text-muted'],
                    'pager'=>['class'=>LinkPager::class],
                    'tableOptions'=>['class'=>'table table-hover table-striped align-middle mb-0','style'=>'min-width:800px'],
                    'headerRowOptions'=>['class'=>'table-light'],
                    'columns'=>[
                        ['attribute'=>'id','headerOptions'=>['style'=>'width:70px']],
						[
							'attribute' => 'role_id',
							'label' => 'Role',
							'value' => function ($model)
							{
								return $model->role->name ?? 'N/A';
							},
						],
						[
							'attribute' => 'department_id',
							'label' => 'Department',
							'value' => function ($model)
							{
								return $model->department->name ?? 'N/A';
							},
						],
						[
							'attribute' => 'page_id',
							'label' => 'Page',
							'value' => function ($model)
							{
								return $model->page->name ?? 'N/A';
							},
						],

                        [
                            'attribute'=>'status','format'=>'raw',
                            'value'=>function ($model)
                            {
                                $active = (int) $model->status === 1;
                                return Html::tag('span',$active ? 'Active' : 'Inactive',[
                                    'class'=>'badge bg-' . ($active ? 'success' : 'secondary'),
                                ]);
                            },
                        ],
                        ['attribute'=>'created_at','format'=>['datetime','php:M d, Y h:i A'],'contentOptions'=>['class'=>'text-nowrap']],
                        [
                            'class'=>yii\grid\ActionColumn::class,
                            'header'=>'Actions',
                            'template'=>'{view} {update}',
                            'headerOptions'=>['class'=>'text-end'],
                            'contentOptions'=>['class'=>'text-end text-nowrap'],
                            'buttons'=>[
                                'view'=>function ($url)
                                {
                                    return Html::a('<i class="bi bi-eye"></i>',$url,['class'=>'btn btn-sm btn-outline-primary me-1','title'=>'View']);
                                },
                                'update'=>function ($url)
                                {
                                    return Html::a('<i class="bi bi-pencil"></i>',$url,['class'=>'btn btn-sm btn-outline-secondary me-1','title'=>'Edit']);
                                },
                                
                            ],
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
