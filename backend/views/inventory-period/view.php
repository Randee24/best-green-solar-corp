
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;
use yii\bootstrap5\BootstrapPluginAsset;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use backend\models\InventoryPeriods;

BootstrapPluginAsset::register($this);

/**
 * @var yii\web\View $this
 * @var InventoryPeriods $model
 */

$this->title = date('F',mktime(0,0,0,(int) $model->month,1)) . ' ' . $model->year;

$this->params['breadcrumbs'][] = [
    'label' => 'Inventory Periods',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

$isClosed = $model->status === 'closed';

$currentYear = (int) date('Y');
$currentMonth = (int) date('n');

$isPastPeriod =
    (int) $model->year < $currentYear ||
    (
        (int) $model->year === $currentYear &&
        (int) $model->month < $currentMonth
    );

$canClose = !$isClosed && $isPastPeriod;

$periodStart = sprintf(
    '%04d-%02d-01',
    (int) $model->year,
    (int) $model->month
);

$periodEnd = date('Y-m-t',strtotime($periodStart));



?>

<div class="inventory-period-view">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">

        <div>
            <h3 class="mb-1">
                <?= Html::encode($this->title) ?>

                <span class="badge bg-<?= $isClosed ? 'secondary' : 'success' ?> fs-6 align-middle ms-2">
                    <?= Html::encode(ucfirst($model->status)) ?>
                </span>
            </h3>

            <div class="text-muted">
                <i class="bi bi-calendar3 me-1"></i>
                <?= Yii::$app->formatter->asDate($periodStart,'php:M d, Y') ?>
                -
                <?= Yii::$app->formatter->asDate($periodEnd,'php:M d, Y') ?>
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
                    'type' => 'button',
                    'data-bs-toggle' => 'offcanvas',
                    'data-bs-target' => '#periodHistory',
                    'aria-controls' => 'periodHistory',
                ]
            ) ?>

            <?php if ($canClose): ?>
                <?= Html::button(
                    '<i class="bi bi-lock me-1"></i> Close Period',
                    [
                        'class' => 'btn btn-danger',
                        'type' => 'button',
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#closePeriodModal',
                    ]
                ) ?>
            <?php endif; ?>

        </div>
    </div>
	
	<!-- PERIOD INFORMATION + SUMMARY WIDGETS -->
    <div class="row g-3 mb-3 align-items-stretch">

        <!-- LEFT: PERIOD INFORMATION -->
        <div class="col-12 col-lg-5">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-3">

                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-info-circle text-primary me-2"></i>
                        Period Information
                    </h6>

                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <span class="text-muted small">Period</span>
                        <span class="fw-semibold">
                            <?= Html::encode($this->title) ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <span class="text-muted small">Period ID</span>
                        <span class="fw-semibold">
                            #<?= Html::encode($model->id) ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                        <span class="text-muted small">Status</span>

                        <span class="badge bg-<?= $isClosed ? 'secondary' : 'success' ?> fs-6 align-middle ms-2">
							<?= Html::encode(ucfirst($model->status)) ?>
						</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 gap-2">
                        <span class="text-muted small">Closed By</span>
                        <span class="fw-semibold text-end small">
                            <?= Html::encode($model->getClosedByDisplayName()) ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-2">
                        <span class="text-muted small">Closed At</span>
                        <span class="fw-semibold small text-end">
                            <?= $model->closed_at
                                ? Yii::$app->formatter->asDatetime($model->closed_at)
                                : '—' ?>
                        </span>
                    </div>

                </div>

            </div>

        </div>

        <!-- RIGHT: FOUR SUMMARY WIDGETS -->
        <div class="col-12 col-lg-7">

            <div class="row row-cols-2 g-2 h-100">

                <!-- OPENING -->
                <div class="col">

                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-box-seam text-primary"></i>
                                <span class="text-muted small"> Closing Inventory Value</span>
                            </div>

                            <h3 class="mb-1">
								₱<?= number_format((float) $balanceSummary['total_closing_value'],2) ?>
							</h3>

							<div class="small text-muted">
								<?= $isClosed ? 'Finalized balance' : 'Not yet finalized' ?>
							</div>

                        </div>
                    </div>

                </div>

                <!-- INVENTORY IN -->
                <div class="col">

                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-box-arrow-in-down text-success"></i>
                                <span class="text-muted small">Inventory IN</span>
                            </div>

                             <h3 class="mb-1">
								<?= number_format($transactionSummary['in_total']) ?>
							</h3>

							<div class="small text-success">
								<?= number_format($transactionSummary['in_posted']) ?> Posted
							</div>

                        </div>
                    </div>

                </div>

                <!-- INVENTORY OUT -->
                <div class="col">

                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-box-arrow-up text-danger"></i>
                                <span class="text-muted small">Inventory OUT</span>
                            </div>

                            <h3 class="mb-1">
								<?= number_format($transactionSummary['out_total']) ?>
							</h3>

							<div class="small text-danger">
								<?= number_format($transactionSummary['out_posted']) ?> Posted
							</div>

                        </div>
                    </div>

                </div>

                <!-- CLOSING -->
                <div class="col">

                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3">

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-boxes text-primary"></i>
                                <span class="text-muted small">Closing Stock</span>
                            </div>

                            <h3 class="mb-1">
								<?= number_format((float) $balanceSummary['total_closing_qty'],3) ?>
							</h3>

							<div class="small text-muted">
								<?= number_format((int) $balanceSummary['product_count']) ?> Products
							</div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                <i class="bi bi-boxes me-2"></i>
                Monthly Product Balances
            </h5>

            <span class="badge bg-secondary">
                <?= number_format((int) $balanceSummary['product_count']) ?> Products
            </span>

        </div>

        <div class="card-body">

            <?php if (!$isClosed): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-1"></i>
                    Product balance snapshots are generated when this period is closed.
                    The quantities below are not live stock balances.
                </div>
            <?php endif; ?>

            <div class="table-responsive">

                <?= GridView::widget([
                    'dataProvider' => $balancesProvider,
                    'tableOptions' => [
                        'class' => 'table table-hover table-striped align-middle',
                    ],
                    'summaryOptions' => [
                        'class' => 'text-muted small mb-3',
                    ],
                    'columns' => [
                        [
                            'attribute' => 'product_name',
                            'label' => 'Product',
                            'value' => function ($row)
                            {
                                return $row['product_name'] ?: 'Product #' . $row['product_id'];
                            },
                        ],
                        [
                            'attribute' => 'opening_qty',
                            'label' => 'Opening',
                            'format' => ['decimal',3],
                            'contentOptions' => ['class' => 'text-end'],
                            'headerOptions' => ['class' => 'text-end'],
                        ],
                        [
                            'attribute' => 'in_qty',
                            'label' => 'IN',
                            'format' => ['decimal',3],
                            'contentOptions' => ['class' => 'text-end text-success'],
                            'headerOptions' => ['class' => 'text-end'],
                        ],
                        [
                            'attribute' => 'out_qty',
                            'label' => 'OUT',
                            'format' => ['decimal',3],
                            'contentOptions' => ['class' => 'text-end text-danger'],
                            'headerOptions' => ['class' => 'text-end'],
                        ],
                        [
                            'attribute' => 'closing_qty',
                            'label' => 'Closing',
                            'format' => ['decimal',3],
                            'contentOptions' => ['class' => 'text-end fw-semibold'],
                            'headerOptions' => ['class' => 'text-end'],
                        ],
                        [
                            'attribute' => 'closing_value',
                            'label' => 'Closing Value',
                            'value' => function ($row)
                            {
                                return '₱' . number_format((float) $row['closing_value'],2);
                            },
                            'contentOptions' => ['class' => 'text-end'],
                            'headerOptions' => ['class' => 'text-end'],
                        ],
                    ],
                ]) ?>

            </div>

        </div>
    </div>

</div>

<!-- HISTORY SIDEBAR -->

<div class="offcanvas offcanvas-end"
     tabindex="-1"
     id="periodHistory"
     aria-labelledby="periodHistoryLabel"
     style="--bs-offcanvas-width: 440px;">

    <div class="offcanvas-header border-bottom">

        <div>
            <h5 class="offcanvas-title mb-1" id="periodHistoryLabel">
                <i class="bi bi-clock-history me-2"></i>
                Period History
            </h5>

            <div class="small text-muted">
                <?= Html::encode($this->title) ?>
            </div>
        </div>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>

    </div>

    <div class="offcanvas-body">

        <?php if (empty($history)): ?>

            <div class="text-center text-muted py-5">
                <i class="bi bi-clock-history fs-1 d-block mb-3"></i>
                No activity found for this period.
            </div>

        <?php else: ?>

            <div class="period-history-timeline">

                <?php foreach ($history as $item): ?>

                    <?php
                    $icon = match ($item['type'])
                    {
                        'in' => 'bi-box-arrow-in-down',
                        'out' => 'bi-box-arrow-up',
                        'closed' => 'bi-lock-fill',
                        default => 'bi-clock-history',
                    };

                    $color = match ($item['type'])
                    {
                        'in' => 'success',
                        'out' => 'danger',
                        'closed' => 'secondary',
                        default => 'primary',
                    };
                    ?>

                    <div class="d-flex gap-3 mb-4">

                        <div class="flex-shrink-0">
                            <div class="rounded-circle bg-<?= $color ?> bg-opacity-10 text-<?= $color ?> d-flex align-items-center justify-content-center"
                                 style="width: 40px; height: 40px;">
                                <i class="bi <?= $icon ?>"></i>
                            </div>
                        </div>

                        <div class="flex-grow-1 border-bottom pb-3">

                            <div class="fw-semibold mb-1">
                                <?php if ($item['url']): ?>
                                    <?= Html::a(
                                        Html::encode($item['title']),
                                        $item['url'],
                                        ['class' => 'text-decoration-none']
                                    ) ?>
                                <?php else: ?>
                                    <?= Html::encode($item['title']) ?>
                                <?php endif; ?>
                            </div>

                            <div class="small text-muted mb-1">
                                <?= Html::encode($item['description']) ?>
                            </div>
							<?php if (!empty($item['user_name'])): ?>
								<div class="small text-muted mb-1">
									<i class="bi bi-person me-1"></i>

									<?= Html::encode($item['user_name']) ?>

									<?php if (!empty($item['department'])): ?>
										(<?= Html::encode($item['department']) ?>)
									<?php endif; ?>
								</div>
							<?php endif; ?>
                            <div class="small text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                <?= Yii::$app->formatter->asDatetime($item['date']) ?>
                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<!-- CLOSE PERIOD MODAL -->

<?php if ($canClose): ?>

<div class="modal fade"
     id="closePeriodModal"
     tabindex="-1"
     aria-labelledby="closePeriodModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <?= Html::beginForm(
                ['close','id' => $model->id],
                'post'
            ) ?>

            <div class="modal-header">

                <h5 class="modal-title" id="closePeriodModalLabel">
                    <i class="bi bi-lock me-2"></i>
                    Close Inventory Period
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    You are about to close
                    <strong><?= Html::encode($this->title) ?></strong>.
                    Once closed, transactions in this period can no longer be modified.
                </div>

                <p class="small text-muted">
                    The system will validate pending transactions and generate
                    the final monthly inventory balance before closing.
                </p>

                <div class="mb-3">
                    <?= Html::label(
                        'Enter your PIN to confirm',
                        'close-pin',
                        ['class' => 'form-label fw-semibold']
                    ) ?>

                    <?= Html::passwordInput(
                        'pin_code',
                        '',
                        [
                            'id' => 'close-pin',
                            'class' => 'form-control',
                            'placeholder' => 'Enter PIN',
                            'required' => true,
                            'autocomplete' => 'off',
                            'inputmode' => 'numeric',
                            'pattern' => '[0-9]{4,8}',
                            'maxlength' => 8,
                        ]
                    ) ?>
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
                    '<i class="bi bi-lock-fill me-1"></i> Confirm Close',
                    ['class' => 'btn btn-danger']
                ) ?>

            </div>

            <?= Html::endForm() ?>

        </div>
    </div>
</div>

<?php endif; ?>
