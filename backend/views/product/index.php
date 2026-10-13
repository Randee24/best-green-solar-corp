<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\ProductsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Products';

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="product-index">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-box-seam text-primary me-2"></i>
                Products
            </h3>

            <p class="text-muted mb-0">
                Manage products, installation pricing, and inventory quantities.
            </p>
        </div>

        <?= Html::a(
            '<i class="bi bi-plus-circle me-1"></i> Add Product',
            ['create'],
            ['class' => 'btn btn-primary']
        ) ?>

    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0">
                <i class="bi bi-list-ul text-primary me-2"></i>
                Product List
            </h5>

        </div>

        <div class="card-body">
			
		
		<?php

		$filterValues = Yii::$app->request->get($searchModel->formName(),[]);

		$hasFilters = !empty(array_filter($filterValues,function ($value)
		{
			return $value !== '' && $value !== null;
		}));

		?>

		<div class="card border shadow-sm mb-3 overflow-hidden">
			<div class="card-header bg-light p-0" id="productSearchHeading">
				<button class="btn w-100 text-start border-0 rounded-0 px-3 py-3 d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#productSearchCollapse" aria-expanded="<?= $hasFilters ? 'true' : 'false' ?>" aria-controls="productSearchCollapse">
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

			<div class="collapse <?= $hasFilters ? 'show' : '' ?>" id="productSearchCollapse">
				<div class="card-body border-top">
					<?= $this->render('_search',[
						'model' => $searchModel,
						'productTypeOptions' => $productTypeOptions,
					]) ?>
				</div>
			</div>
		</div>




            <?php Pjax::begin([
                'id' => 'products-grid',
                'timeout' => 10000,
                'enablePushState' => true,
            ]); ?>
		
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
                        'label' => 'ID',
                        'contentOptions' => [
                            'style' => 'width:70px;',
                        ],
                    ],

                    [
                        'attribute' => 'name',
                        'label' => 'Product',
                        'format' => 'raw',
                        'value' => function($model)
                        {
                            return Html::a(
                                Html::encode($model->name),
                                ['view','id' => $model->id],
                                ['class' => 'fw-semibold text-decoration-none']
                            )
                            . '<div class="text-muted small">'
                            . Html::encode($model->description)
                            . '</div>';
                        },
                    ],

                    [
                        'attribute' => 'model',
                        'label' => 'Model',
                        'value' => function($model)
                        {
                            return $model->model ?: '—';
                        },
                    ],

                    [
                        'attribute' => 'product_type_name',
                        'label' => 'Type',
                        'value' => function($model)
                        {
                            return $model->productType->name ?? '—';
                        },
                    ],

                    [
                        'attribute' => 'unit_name',
                        'label' => 'Unit',
                        'value' => function($model)
                        {
                            return $model->unit->name ?? '—';
                        },
                    ],

                    [
                        'label' => 'Qty',
                        'format' => 'raw',
                        'value' => function($model)
                        {
                            $qty = $model->getAvailableQty();

                            $class = $qty > 0 ? 'text-success' : 'text-danger';

                            return '<span class="fw-bold ' . $class . '">'
                                . number_format($qty,3)
                                . '</span>';
                        },
                        'contentOptions' => [
                            'class' => 'text-end',
                        ],
                    ],

                    [
                        'attribute' => 'installation_price',
                        'label' => 'Installation Price',
                        'format' => 'raw',
                        'value' => function($model)
                        {
                            return '₱' . number_format(
                                (float) $model->installation_price,
                                2
                            );
                        },
                        'contentOptions' => [
                            'class' => 'text-end',
                        ],
                    ],

                    [
                        'attribute' => 'created_at',
                        'label' => 'Created',
                        'format' => [
                            'date',
                            'php:M d, Y',
                        ],
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
                                        'title' => 'View Product',
                                        'data-pjax' => '0',
                                    ]
                                );
                            },

                            'update' => function($url,$model)
                            {
                                return Html::a(
                                    '<i class="bi bi-pencil"></i>',
                                    ['update','id' => $model->id],
                                    [
                                        'class' => 'btn btn-sm btn-outline-secondary',
                                        'title' => 'Edit Product',
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