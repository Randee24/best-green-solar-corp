
<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var backend\models\InventoryPeriods $model */
/** @var yii\data\ActiveDataProvider $inventoryInProvider */

$this->title = $model->getPeriodName();

$this->params['breadcrumbs'][] = [
    'label' => 'Inventory Periods',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

$isOpen = $model->status === 'open';

?>

<div class="inventory-period-view">

    <?= $this->render('//layouts/_flash') ?>

    <!-- HEADER -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-calendar3 text-primary me-2"></i>
                <?= Html::encode($model->getPeriodName()) ?>
            </h3>

            <div class="d-flex align-items-center gap-2">
                <span class="text-muted">
                    Period ID: #<?= Html::encode($model->id) ?>
                </span>

                <span class="badge bg-<?= $isOpen ? 'success' : 'secondary' ?>">
                    <i class="bi bi-<?= $isOpen ? 'unlock' : 'lock' ?> me-1"></i>
                    <?= ucfirst($model->status) ?>
                </span>
            </div>
        </div>

        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i> Back to Periods',
            ['index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>

    </div>

    <!-- SUMMARY CARDS -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-receipt fs-3"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Total Inventory IN</div>

                        <h3 class="fw-bold mb-0">
                            <?= number_format($model->getInventoryInCount()) ?>
                        </h3>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-check-circle fs-3"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Posted Receipts</div>

                        <h3 class="fw-bold mb-0">
                            <?= number_format($model->getPostedInventoryInCount()) ?>
                        </h3>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>

                    <div>
                        <div class="text-muted small">Posted IN Value</div>

                        <h3 class="fw-bold mb-0">
                            ₱<?= number_format((float) $model->getInventoryInTotal(),2) ?>
                        </h3>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- PERIOD INFORMATION -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-info-circle text-primary me-2"></i>
                Period Information
            </h5>
        </div>

        <div class="card-body p-4">

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="text-muted small mb-1">Year</div>
                    <div class="fw-semibold">
                        <?= Html::encode($model->year) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small mb-1">Month</div>
                    <div class="fw-semibold">
                        <?= date('F',mktime(0,0,0,(int) $model->month,1)) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small mb-1">Status</div>
                    <div class="fw-semibold">
                        <?= ucfirst(Html::encode($model->status)) ?>
                    </div>
                </div>

                <?php if (!$isOpen)
                { ?>
                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Closed At</div>

                        <div class="fw-semibold">
                            <?= $model->closed_at
                                ? Yii::$app->formatter->asDatetime($model->closed_at,'php:M d, Y h:i A')
                                : 'N/A' ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small mb-1">Closed By (User ID)</div>

                        <div class="fw-semibold">
                            <?= Html::encode($model->closed_by ?? 'N/A') ?>
                        </div>
                    </div>
                <?php } ?>

            </div>

        </div>
    </div>

    <!-- PERIOD STATUS NOTICE -->
    <?php if ($isOpen)
    { ?>
        <div class="alert alert-success">
            <i class="bi bi-unlock me-2"></i>
            This inventory period is open and can accept new inventory transactions.
        </div>
    <?php }
    else
    { ?>
        <div class="alert alert-secondary">
            <i class="bi bi-lock me-2"></i>
            This inventory period is closed. Inventory transactions for this period should no longer be modified.
        </div>
    <?php } ?>

    <!-- INVENTORY IN TRANSACTIONS -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-box-arrow-in-down text-primary me-2"></i>
                Inventory IN Transactions
            </h5>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <?= GridView::widget([
                    'dataProvider' => $inventoryInProvider,
                    'layout' => "{items}\n<div class=\"px-3 py-2\">{summary}{pager}</div>",
                    'emptyText' => 'No Inventory IN transactions found for this period.',
                    'tableOptions' => [
                        'class' => 'table table-hover align-middle mb-0',
                    ],
                    'columns' => [

                        [
                            'label' => 'Receipt',
                            'format' => 'raw',
                            'value' => function($receipt)
                            {
                                return Html::a(
                                    'IN-' . str_pad($receipt->id,5,'0',STR_PAD_LEFT),
                                    ['/inventory-in/view','id' => $receipt->id],
                                    ['class' => 'fw-semibold text-decoration-none']
                                );
                            },
                            'contentOptions' => [
                                'class' => 'ps-3',
                            ],
                        ],

                        [
                            'attribute' => 'transaction_date',
                            'label' => 'Date',
                            'format' => ['date','php:M d, Y'],
                        ],

                        [
                            'label' => 'Supplier',
                            'value' => function($receipt)
                            {
                                return $receipt->supplier->name ?? 'N/A';
                            },
                        ],

                        [
                            'label' => 'Products',
                            'value' => function($receipt)
                            {
                                return count($receipt->inventoryInItems);
                            },
                            'contentOptions' => [
                                'class' => 'text-center',
                            ],
                        ],

                        [
                            'label' => 'Total Amount',
                            'value' => function($receipt)
                            {
                                $total = '0.00';

                                foreach ($receipt->inventoryInItems as $item)
                                {
                                    $total = bcadd($total,(string) $item->total_amount,2);
                                }

                                return '₱' . number_format((float) $total,2);
                            },
                            'contentOptions' => [
                                'class' => 'text-end',
                            ],
                        ],

                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function($receipt)
                            {
                                $classes = [
                                    'draft' => 'warning',
                                    'posted' => 'success',
                                    'reversed' => 'secondary',
                                ];

                                $class = $classes[$receipt->status] ?? 'secondary';

                                return '<span class="badge bg-' . $class . '">'
                                    . ucfirst(Html::encode($receipt->status))
                                    . '</span>';
                            },
                            'contentOptions' => [
                                'class' => 'pe-3',
                            ],
                        ],

                    ],
                ]) ?>

            </div>

        </div>
    </div>

</div>
