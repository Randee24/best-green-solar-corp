
<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\Products $model */

$this->title = $model->name;

$this->params['breadcrumbs'][] = [
    'label' => 'Products',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;


?>
<?php
$this->registerCss(<<<CSS
.supplier-price-history-table th,
.supplier-price-history-table td {
    padding: 12px 14px;
    vertical-align: middle;
}

.supplier-price-history-table thead th {
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
    border-bottom: 1px solid #dee2e6;
}

.supplier-price-history-table tbody td {
    font-size: 13px;
}

.supplier-price-history-table tbody tr:hover {
    background-color: rgba(13,110,253,0.035);
}
CSS);
?>
<div class="product-view">
    <?= $this->render('//layouts/_flash') ?>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3 p-lg-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <h4 class="fw-bold mb-1">
                        <i class="bi bi-box-seam text-primary me-2"></i>
                        <?= Html::encode($model->name) ?>
                    </h4>
                    <div class="text-muted small">Product ID: #<?= Html::encode($model->id) ?></div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <?= Html::a(
                        '<i class="bi bi-arrow-left me-1"></i> Back',
                        ['index'],
                        ['class' => 'btn btn-outline-secondary']
                    ) ?>
                    <?= Html::a(
                        '<i class="bi bi-pencil-square me-1"></i> Edit Product',
                        ['update','id' => $model->id],
                        ['class' => 'btn btn-primary']
                    ) ?>
                </div>
            </div>

            <div class="row g-3">
                <!-- LEFT: PRODUCT DETAILS -->
                <div class="col-lg-8">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="text-muted small mb-1">Model</div>
                            <div class="fw-semibold"><?= Html::encode($model->model ?: 'N/A') ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small mb-1">Product Type</div>
                            <div class="fw-semibold"><?= Html::encode($model->productType->name ?? 'N/A') ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small mb-1">Unit</div>
                            <div class="fw-semibold"><?= Html::encode($model->unit->name ?? 'N/A') ?></div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small mb-1">Description</div>
                            <div><?= $model->description ? nl2br(Html::encode($model->description)) : '<span class="text-muted">N/A</span>' ?></div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: INVENTORY / PRICE / DATE -->
                <div class="col-lg-4">
                    <div class="bg-light rounded-3 p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center gap-2 pb-2 mb-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-boxes me-1"></i> Available Qty</span>
                            <span class="fw-bold <?= $qty > 0 ? 'text-success' : 'text-danger' ?>">
                                <?= number_format($qty,3) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2 pb-2 mb-2 border-bottom">
                            <span class="text-muted small"><i class="bi bi-cash-stack me-1"></i> Installation Price</span>
                            <span class="fw-bold">₱<?= number_format((float) $model->installation_price,2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <span class="text-muted small"><i class="bi bi-calendar-check me-1"></i> Date Added</span>
                            <span class="fw-semibold text-end">
                                <?= Yii::$app->formatter->asDate($model->created_at,'php:M d, Y') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SUPPLIER PRICE CHANGE HISTORY -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">

            <div>
                <h5 class="mb-1">
                    <i class="bi bi-clock-history text-primary me-2"></i>
                    Supplier Price Change History
                </h5>

                <small class="text-muted">
                    Recorded supplier price adjustments for this product.
                </small>
            </div>

            <span class="badge bg-primary">
                <?= $priceChangeCount ?> Changes
            </span>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <?= GridView::widget([
                    'dataProvider' => $historyProvider,
                    'layout' => "{items}\n<div class=\"px-3 py-2\">{summary}{pager}</div>",
                    'emptyText' => 'No supplier price changes recorded for this product.',
                    'tableOptions' => [
						'class' => 'table table-hover table-striped align-middle mb-0 supplier-price-history-table',
						'style' => 'min-width: 1050px; table-layout: fixed;',
					],
					'headerRowOptions' => [
						'class' => 'table-light',
					],
                    
					'columns' => [
						[
							'label' => 'Date Changed',
							'value' => function($history)
							{
								return Yii::$app->formatter->asDatetime(
									$history->changed_at,
									'php:M d, Y h:i A'
								);
							},
							'headerOptions' => [
								'style' => 'width: 17%;',
								'class' => 'ps-3 text-nowrap',
							],
							'contentOptions' => [
								'class' => 'ps-3 text-nowrap',
							],
						],
						[
							'label' => 'Supplier',
							'format' => 'text',
							'value' => function($history)
							{
								return $history->supplierProduct->supplier->name ?? 'N/A';
							},
							'headerOptions' => [
								'style' => 'width: 18%;',
							],
							'contentOptions' => [
								'class' => 'text-truncate',
								'style' => 'max-width: 0;',
							],
						],
						[
							'label' => 'Supplier Code',
							'format' => 'text',
							'value' => function($history)
							{
								return $history->supplierProduct->supplier_product_code ?? '—';
							},
							'headerOptions' => [
								'style' => 'width: 14%;',
							],
							'contentOptions' => [
								'class' => 'text-truncate',
								'style' => 'max-width: 0;',
							],
						],
						[
							'label' => 'Previous Price',
							'format' => 'raw',
							'value' => function($history)
							{
								return '<span class="text-muted">₱'
									. number_format((float) $history->old_price,2)
									. '</span>';
							},
							'headerOptions' => [
								'style' => 'width: 12%;',
								'class' => 'text-end text-nowrap',
							],
							'contentOptions' => [
								'class' => 'text-end text-nowrap',
							],
						],
						[
							'label' => 'New Price',
							'format' => 'raw',
							'value' => function($history)
							{
								return '<span class="fw-semibold">₱'
									. number_format((float) $history->new_price,2)
									. '</span>';
							},
							'headerOptions' => [
								'style' => 'width: 12%;',
								'class' => 'text-end text-nowrap',
							],
							'contentOptions' => [
								'class' => 'text-end text-nowrap',
							],
						],
						[
							'label' => 'Difference',
							'format' => 'raw',
							'value' => function($history)
							{
								$difference = round(
									(float) $history->new_price - (float) $history->old_price,
									2
								);

								if ($difference > 0)
								{
									return '<span class="text-danger fw-semibold">'
										. '<i class="bi bi-arrow-up-short"></i> ₱'
										. number_format($difference,2)
										. '</span>';
								}

								if ($difference < 0)
								{
									return '<span class="text-success fw-semibold">'
										. '<i class="bi bi-arrow-down-short"></i> ₱'
										. number_format(abs($difference),2)
										. '</span>';
								}

								return '<span class="text-muted">No Change</span>';
							},
							'headerOptions' => [
								'style' => 'width: 13%;',
								'class' => 'text-end text-nowrap',
							],
							'contentOptions' => [
								'class' => 'text-end text-nowrap',
							],
						],
						[
							'label' => 'Changed By',
							'value' => function($history)
							{
								return $history->user->username ?? 'System';
							},
							'headerOptions' => [
								'style' => 'width: 14%;',
								'class' => 'pe-3',
							],
							'contentOptions' => [
								'class' => 'pe-3 text-truncate',
								'style' => 'max-width: 0;',
							],
						],
					],

                ]) ?>

            </div>

        </div>
    </div>

</div>
