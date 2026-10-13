
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var backend\models\Suppliers $model */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = $model->name;

$this->params['breadcrumbs'][] = [
    'label' => 'Suppliers',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="supplier-view">
	<?php foreach (['success','error','warning'] as $type)
	{ ?>
		<?php if (Yii::$app->session->hasFlash($type))
		{ ?>
			<div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">

				<i class="bi bi-<?= $type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill' ?> me-2"></i>

				<?= \yii\helpers\Html::encode(Yii::$app->session->getFlash($type)) ?>

				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

			</div>
		<?php } ?>
	<?php } ?>
    <!-- PAGE HEADER -->
     
	<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">

		<!-- LEFT: SUPPLIER INFORMATION -->
		<div>

			<h4 class="fw-semibold mb-1">
				<i class="bi bi-building me-2 text-primary"></i>
				<?= Html::encode($model->name) ?>
			</h4>

			<div class="d-flex flex-wrap gap-3 text-muted small">

				<span>
					<i class="bi bi-hash me-1"></i>
					ID: <?= Html::encode($model->id) ?>
				</span>

				<span>
					<i class="bi bi-calendar3 me-1"></i>
					Date Added:
					<?= Yii::$app->formatter->asDate($model->created_at,'php:M d, Y') ?>
				</span>

			</div>

		</div>

		<!-- RIGHT: ACTION BUTTONS -->
		<div class="d-flex flex-wrap gap-2">

			<?= Html::a(
				'<i class="bi bi-arrow-left me-1"></i> Back',
				['index'],
				['class' => 'btn btn-outline-secondary']
			) ?>

			<?= Html::a(
				'<i class="bi bi-pencil-square me-1"></i> Update',
				['update','id' => $model->id],
				['class' => 'btn btn-primary']
			) ?>

			

			<?= Html::endForm() ?>

		</div>

	</div>


    
	<!-- SUPPLIER PRODUCTS -->
	<div class="card border-0 shadow-sm">

		<div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">

			<div>
				<h5 class="mb-1">
					<i class="bi bi-box-seam text-primary me-2"></i>
					Supplier Products
				</h5>

				<small class="text-muted">
					Search products registered under this supplier.
				</small>
			</div>

			<span class="badge bg-primary">
				<?= number_format($dataProvider->getTotalCount()) ?> Products
			</span>

		</div>

		<div class="card-body p-0">

			<?php Pjax::begin([
				'id' => 'supplier-products-grid',
				'timeout' => 10000,
				'enablePushState' => true,
			]); ?>

			<div class="table-responsive">

				<?= GridView::widget([
					'dataProvider' => $dataProvider,
					'filterModel' => $searchModel,
					'layout' => "{items}\n<div class=\"px-3 py-2\">{summary}{pager}</div>",
					'emptyText' => 'No matching supplier products found.',
					'tableOptions' => [
						'class' => 'table table-hover align-middle mb-0',
					],
					'filterRowOptions' => [
						'class' => 'table-light',
					],
					'columns' => [

						[
							'attribute' => 'product_name',
							'label' => 'Product',
							'format' => 'raw',
							'value' => function($supplierProduct)
							{
								$product = $supplierProduct->product;

								if ($product === null)
								{
									return '<span class="text-muted">Unknown Product</span>';
								}

								return Html::a(
									Html::encode($product->name),
									['/product/view','id' => $product->id],
									[
										'class' => 'fw-semibold text-decoration-none',
										'data-pjax' => '0',
									]
								)
								. '<div class="text-muted small">'
								. Html::encode($product->description)
								. '</div>';
							},
							'filterInputOptions' => [
								'class' => 'form-control form-control-sm',
								'placeholder' => 'Search product...',
							],
							'contentOptions' => [
								'class' => 'ps-3',
							],
						],

						[
							'attribute' => 'product_model',
							'label' => 'Model',
							'value' => function($supplierProduct)
							{
								return $supplierProduct->product->model ?? '—';
							},
							'filterInputOptions' => [
								'class' => 'form-control form-control-sm',
								'placeholder' => 'Search model...',
							],
						],

						[
							'attribute' => 'supplier_product_code',
							'label' => 'Supplier Code',
							'format' => 'raw',
							'value' => function($supplierProduct)
							{
								if (empty($supplierProduct->supplier_product_code))
								{
									return '<span class="text-muted">—</span>';
								}

								return '<span class="badge bg-light text-dark border">'
									. Html::encode($supplierProduct->supplier_product_code)
									. '</span>';
							},
							'filterInputOptions' => [
								'class' => 'form-control form-control-sm',
								'placeholder' => 'Search code...',
							],
						],

						[
							'attribute' => 'supplier_price',
							'label' => 'Latest Price',
							'format' => 'raw',
							'value' => function($supplierProduct)
							{
								return '<span class="fw-semibold">₱'
									. number_format((float) $supplierProduct->supplier_price,2)
									. '</span>';
							},
							'filterInputOptions' => [
								'class' => 'form-control form-control-sm',
								'placeholder' => 'Exact price...',
							],
							'contentOptions' => [
								'class' => 'text-end',
							],
						],

						[
							'attribute' => 'created_at',
							'label' => 'Price Updated',
							'format' => 'raw',
							'value' => function($supplierProduct)
							{
								return Yii::$app->formatter->asDatetime(
									$supplierProduct->created_at,
									'php:M d, Y h:i A'
								);
							},
							'filter' => false,
							'contentOptions' => [
								'style' => 'white-space:nowrap;',
							],
						],

						[
							'label' => 'Actions',
							'format' => 'raw',
							'value' => function($supplierProduct)
							{
								return Html::a(
									'<i class="bi bi-eye"></i>',
									['/product/view','id' => $supplierProduct->product_id],
									[
										'class' => 'btn btn-sm btn-outline-primary',
										'title' => 'View Product',
										'data-pjax' => '0',
									]
								);
							},
							'contentOptions' => [
								'class' => 'pe-3 text-center',
							],
						],

					],
				]) ?>

			</div>

			<?php Pjax::end(); ?>

		</div>

	</div>

</div>
