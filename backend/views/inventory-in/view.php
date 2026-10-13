
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use yii\bootstrap5\BootstrapPluginAsset;

/**
 * @var yii\web\View $this
 * @var backend\models\InventoryIns $model
 */

BootstrapPluginAsset::register($this);

$this->title = 'Inventory IN #' . $model->id;

$this->params['breadcrumbs'][] = [
    'label' => 'Inventory IN',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

$items = $model->inventoryInItems;

$itemsProvider = new \yii\data\ArrayDataProvider([
    'allModels' => $items,
    'pagination' => [
        'pageSize' => 50,
    ],
]);

$totalQty = 0;
$totalAmount = 0;

foreach ($items as $item)
{
    $totalQty += (float) $item->qty;
    $totalAmount += (float) $item->total_amount;
}

$isPosted = $model->status === 'posted';

$statusClass = match ($model->status)
{
    'posted' => 'success',
    'draft' => 'warning',
    'reversed' => 'danger',
    default => 'secondary',
};

?>

<div class="inventory-in-view">

    <?= $this->render('//layouts/_flash') ?>

    <!-- HEADER -->

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">

        <div>
            <h3 class="mb-1">
                <?= Html::encode($this->title) ?>

                <span class="badge bg-<?= $statusClass ?> fs-6 align-middle ms-2">
                    <?= Html::encode(ucfirst($model->status)) ?>
                </span>
            </h3>

            <div class="text-muted small">
                <i class="bi bi-calendar3 me-1"></i>
                <?= Yii::$app->formatter->asDate($model->transaction_date) ?>
            </div>
        </div>

				
		<div class="d-flex flex-wrap gap-2">

			<?= Html::a(
				'<i class="bi bi-arrow-left me-1"></i> Back',
				['index'],
				['class' => 'btn btn-outline-secondary']
			) ?>

			<?= Html::button(
				'<i class="bi bi-clock-history me-1"></i> History',
				[
					'class' => 'btn btn-outline-primary',
					'data-bs-toggle' => 'offcanvas',
					'data-bs-target' => '#inventoryInHistory',
				]
			) ?>

			<?php if ($model->status === 'draft'): ?>

				<?= Html::a(
					'<i class="bi bi-pencil me-1"></i> Edit',
					['update','id' => $model->id],
					['class' => 'btn btn-outline-primary']
				) ?>

				<?= Html::beginForm(
					['post','id' => $model->id],
					'post',
					['class' => 'd-inline']
				) ?>


				<?= Html::button(
					'<i class="bi bi-check-circle me-1"></i> Post Inventory IN',
					[
						'class' => 'btn btn-success',
						'data-bs-toggle' => 'modal',
						'data-bs-target' => '#postInventoryModal',
					]
				) ?>


				<?= Html::endForm() ?>

			<?php endif; ?>

		</div>


    </div>

    <!-- COMPACT TRANSACTION SUMMARY -->

    <div class="card shadow-sm mb-3">

        <div class="card-body py-3">

            <div class="row g-3 align-items-center">

                <div class="col-md-3">
                    <div class="text-muted small">Supplier</div>
                    <div class="fw-semibold">
                        <?= Html::encode($model->supplier->name ?? 'N/A') ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="text-muted small">Received By</div>
                    <div class="fw-semibold">
                        <?= Html::encode($model->getReceivedByDisplayName()) ?>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="text-muted small">Total Products</div>
                    <div class="fw-semibold">
                        <?= number_format(count($items)) ?>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="text-muted small">Total Quantity</div>
                    <div class="fw-semibold">
                        <?= number_format($model->getTotalReceivedQty()) ?>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="text-muted small">Total Amount</div>
                    <div class="fw-semibold text-success">
                        ₱<?= number_format($model->getTotalReceivedAmount(),2) ?>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- FULL-WIDTH PRODUCT TABLE -->

    <div class="card shadow-sm">

        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">

            <h5 class="mb-0">
                <i class="bi bi-box-seam me-2"></i>
                Products
            </h5>

            <span class="badge bg-secondary">
                <?= number_format(count($items)) ?> Items
            </span>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <?= GridView::widget([
                    'dataProvider' => $itemsProvider,
                    'tableOptions' => [
                        'class' => 'table table-hover table-striped align-middle',
                    ],
                    'summaryOptions' => [
                        'class' => 'text-muted small mb-3',
                    ],
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                            'headerOptions' => ['style' => 'width: 55px'],
                        ],
                        [
                            'label' => 'Product',
                            'format' => 'raw',
                            'value' => function ($item)
                            {
                                $name = $item->product->name ?? 'Unknown Product';
                                $model = $item->product->model ?? '';

                                return '<div class="fw-semibold">' .
                                    Html::encode($name) .
                                    '</div>' .
                                    (
                                        $model !== ''
                                        ? '<div class="small text-muted">' .
                                            Html::encode($model) .
                                          '</div>'
                                        : ''
                                    );
                            },
                        ],
                        [
                            'label' => 'Unit',
                            'value' => function ($item)
                            {
                                return $item->product->unit->name ?? '-';
                            },
                        ],
                        [
                            'attribute' => 'qty',
                            'label' => 'Quantity',
                            'value' => function ($item)
                            {
                                return number_format((float) $item->qty,3);
                            },
                            'contentOptions' => ['class' => 'text-end'],
                            'headerOptions' => ['class' => 'text-end'],
                        ],
                        [
                            'attribute' => 'amount',
                            'label' => 'Unit Price',
                            'value' => function ($item)
                            {
                                return '₱' . number_format((float) $item->amount,2);
                            },
                            'contentOptions' => ['class' => 'text-end'],
                            'headerOptions' => ['class' => 'text-end'],
                        ],
                        [
                            'attribute' => 'total_amount',
                            'label' => 'Total',
                            'value' => function ($item)
                            {
                                return '₱' . number_format((float) $item->total_amount,2);
                            },
                            'contentOptions' => ['class' => 'text-end fw-semibold'],
                            'headerOptions' => ['class' => 'text-end'],
                        ],
                    ],
                ]) ?>

            </div>

        </div>

    </div>

</div>
<?php if ($model->status === 'draft'): ?>

		<div class="modal fade"
			 id="postInventoryModal"
			 tabindex="-1"
			 aria-labelledby="postInventoryModalLabel"
			 aria-hidden="true">

			<div class="modal-dialog modal-dialog-centered">

				<div class="modal-content">

					<?= Html::beginForm(
						['post','id' => $model->id],
						'post'
					) ?>

						<div class="modal-header">

							<h5 class="modal-title" id="postInventoryModalLabel">
								<i class="bi bi-shield-lock me-2"></i>
								Confirm Inventory Posting
							</h5>

							<button type="button"
									class="btn-close"
									data-bs-dismiss="modal"
									aria-label="Close">
							</button>

						</div>

						<div class="modal-body">

							<p class="text-muted mb-3">
								You are about to post Inventory IN
								<strong>#<?= Html::encode($model->id) ?></strong>.
								Please enter your PIN to confirm.
							</p>

							<label for="posting-pin" class="form-label fw-semibold">
								Security PIN
							</label>

							<?= Html::passwordInput(
								'pin_code',
								'',
								[
									'id' => 'posting-pin',
									'class' => 'form-control',
									'placeholder' => 'Enter your PIN',
									'autocomplete' => 'off',
									'inputmode' => 'numeric',
									'required' => true,
								]
							) ?>

							<div class="form-text">
								Your PIN will be verified before posting.
							</div>

							<div class="alert alert-warning mt-3 mb-0 small">
								<i class="bi bi-exclamation-triangle me-1"></i>
								Posting will update available inventory quantities.
								This action cannot be edited afterward.
							</div>

						</div>

						<div class="modal-footer">

							<?= Html::button(
								'Cancel',
								[
									'class' => 'btn btn-outline-secondary',
									'data-bs-dismiss' => 'modal',
								]
							) ?>

							<?= Html::submitButton(
								'<i class="bi bi-check-circle me-1"></i> Confirm Posting',
								[
									'class' => 'btn btn-success',
								]
							) ?>

						</div>

					<?= Html::endForm() ?>

				</div>

			</div>

		</div>

	<?php endif; ?>
<!-- HISTORY SIDEBAR -->

<div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="inventoryInHistory"
     aria-labelledby="inventoryInHistoryLabel"
     style="--bs-offcanvas-width: 420px;">

    <div class="offcanvas-header border-bottom">

        <h5 class="offcanvas-title" id="inventoryInHistoryLabel">
            <i class="bi bi-clock-history me-2"></i>
            Inventory IN History
        </h5>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>

    </div>

    
	<div class="offcanvas-body">

		<?php if (empty($history)): ?>

			<div class="text-muted text-center py-4">
				No history available.
			</div>

		<?php else: ?>

			<div class="border-start ps-3">

				<?php foreach ($history as $item): ?>

					<?php
						$icon = match ($item['type'])
						{
							'created' => 'bi-plus-circle text-primary',
							'updated' => 'bi-pencil-square text-warning',
							'posted' => 'bi-check-circle text-success',
							default => 'bi-clock-history text-secondary',
						};
					?>

					<div class="mb-4">

						<div class="fw-semibold">
							<i class="bi <?= $icon ?> me-1"></i>
							<?= Html::encode($item['title']) ?>
						</div>

						<div class="small text-muted mb-2">
							<?= Yii::$app->formatter->asDatetime($item['date']) ?>
						</div>

						<?php if (!empty($item['old_value']) || !empty($item['new_value'])): ?>

							<div class="border rounded p-2 bg-light mb-2">

								<div class="small text-muted mb-1">
									Previous Value
								</div>

								<div class="small text-danger mb-2">
									<?= Html::encode($item['old_value'] ?? '-') ?>
								</div>

								<div class="small text-muted mb-1">
									New Value
								</div>

								<div class="small text-success">
									<?= Html::encode($item['new_value'] ?? '-') ?>
								</div>

							</div>

						<?php else: ?>

							<div class="small text-muted mb-2">
								<?= Html::encode($item['description'] ?? '') ?>
							</div>

						<?php endif; ?>

						<?php if (!empty($item['user_name'])): ?>

							<div class="small text-muted">

								<i class="bi bi-person me-1"></i>

								<?= Html::encode($item['user_name']) ?>

								<?php if (!empty($item['department'])): ?>
									(<?= Html::encode($item['department']) ?>)
								<?php endif; ?>

							</div>

						<?php endif; ?>

					</div>

				<?php endforeach; ?>

			</div>

		<?php endif; ?>

	</div>
	
	


</div>
