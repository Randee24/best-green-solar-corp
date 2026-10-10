
<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var backend\models\InventoryIns $model */

$this->title = 'IN-' . str_pad($model->id,5,'0',STR_PAD_LEFT);

$this->params['breadcrumbs'][] = [
    'label' => 'Inventory IN',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

$status = strtolower((string) $model->status);

$statusClasses = [
    'draft' => 'bg-secondary',
    'posted' => 'bg-success',
    'reversed' => 'bg-danger',
];

$statusClass = $statusClasses[$status] ?? 'bg-secondary';

$items = $model->inventoryInItems;

$totalQuantity = 0;
$grandTotal = 0;

foreach ($items as $item)
{
    $totalQuantity += (float) $item->qty;
    $grandTotal += (float) $item->total_amount;
}

$period = $model->inventoryPeriod;
$isPeriodOpen = $period !== null && $period->status === 'open';

$canModify = $status === 'draft' && $isPeriodOpen;

$supplierName = $model->supplier->name ?? 'N/A';

?>

<div class="inventory-in-view">

    <!-- PAGE HEADER -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-box-arrow-in-down text-primary me-2"></i>
                Inventory Receipt
            </h3>

            <div class="text-muted">
                Reference:
                <span class="fw-semibold">
                    <?= Html::encode($this->title) ?>
                </span>

                <span class="badge <?= $statusClass ?> ms-2">
                    <?= Html::encode(strtoupper($status)) ?>
                </span>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">

            <?= Html::a(
                '<i class="bi bi-arrow-left me-1"></i> Back',
                ['index'],
                ['class' => 'btn btn-outline-secondary']
            ) ?>

            <?php if ($canModify)
            { ?>

                <?= Html::a(
                    '<i class="bi bi-pencil-square me-1"></i> Edit',
                    ['update','id' => $model->id],
                    ['class' => 'btn btn-outline-primary']
                ) ?>

                <?= Html::beginForm(
                    ['post','id' => $model->id],
                    'post',
                    ['class' => 'd-inline']
                ) ?>

                <?= Html::submitButton(
                    '<i class="bi bi-check-circle me-1"></i> Post Inventory',
                    [
                        'class' => 'btn btn-success',
                        'data' => [
                            'confirm' => 'Are you sure you want to post this inventory receipt? Once posted, it cannot be edited directly.',
                        ],
                    ]
                ) ?>

                <?= Html::endForm() ?>

            <?php } ?>

        </div>
    </div>

    <!-- FLASH MESSAGES -->
    <?php foreach (['success','error','warning'] as $flashType)
    {
        if (Yii::$app->session->hasFlash($flashType))
        {
            $alertClass = $flashType === 'error' ? 'danger' : $flashType;
            ?>

            <div class="alert alert-<?= $alertClass ?> alert-dismissible fade show" role="alert">

                <?= Html::encode(Yii::$app->session->getFlash($flashType)) ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                </button>

            </div>

            <?php
        }
    } ?>

    <!-- STATUS INFORMATION -->
    <?php if ($status === 'draft')
    { ?>

        <div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>

            <div>
                <strong>Draft Inventory Receipt</strong>

                <div class="small mt-1">
                    This receipt has not been posted yet.
                    Its products are not included in the available inventory stock.
                </div>
            </div>
        </div>

    <?php }
    elseif ($status === 'posted')
    { ?>

        <div class="alert alert-success d-flex align-items-start gap-2 mb-4">
            <i class="bi bi-check-circle-fill fs-5"></i>

            <div>
                <strong>Inventory Successfully Posted</strong>

                <div class="small mt-1">
                    The received products are now included in inventory
                    and available for FIFO stock allocation.
                </div>
            </div>
        </div>

    <?php }
    elseif ($status === 'reversed')
    { ?>

        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4">
            <i class="bi bi-arrow-counterclockwise fs-5"></i>

            <div>
                <strong>Reversed Inventory Receipt</strong>

                <div class="small mt-1">
                    This receipt has been reversed and should not contribute
                    to the current available inventory balance.
                </div>
            </div>
        </div>

    <?php } ?>

    <?php if (!$isPeriodOpen && $status === 'draft')
    { ?>

        <div class="alert alert-danger mb-4">
            <i class="bi bi-lock-fill me-2"></i>
            This inventory period is closed or unavailable.
            The receipt cannot be edited or posted.
        </div>

    <?php } ?>

    <!-- RECEIPT INFORMATION -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-info-circle me-2 text-primary"></i>
                Receipt Information
            </h5>
        </div>

        <div class="card-body p-4">

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="text-muted small mb-1">
                        Reference Number
                    </div>

                    <div class="fw-semibold">
                        <?= Html::encode($this->title) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small mb-1">
                        Transaction Date
                    </div>

                    <div class="fw-semibold">
                        <?= Yii::$app->formatter->asDate(
                            $model->transaction_date,
                            'php:F d, Y'
                        ) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small mb-1">
                        Inventory Period
                    </div>

                    <div class="fw-semibold">
                        <?php if ($period !== null)
                        { ?>

                            <?= Html::encode(
                                date('F',mktime(0,0,0,(int) $period->month,1))
                                . ' ' . $period->year
                            ) ?>

                            <span class="badge <?= $isPeriodOpen ? 'bg-success' : 'bg-secondary' ?> ms-1">
                                <?= Html::encode(ucfirst($period->status)) ?>
                            </span>

                        <?php }
                        else
                        { ?>
                            N/A
                        <?php } ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small mb-1">
                        Supplier
                    </div>

                    <div class="fw-semibold">
                        <i class="bi bi-building me-1 text-muted"></i>
                        <?= Html::encode($supplierName) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small mb-1">
                        Created At
                    </div>

                    <div class="fw-semibold">
                        <?= $model->created_at
                            ? Yii::$app->formatter->asDatetime(
                                $model->created_at,
                                'php:M d, Y h:i A'
                            )
                            : 'N/A' ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small mb-1">
                        Status
                    </div>

                    <span class="badge <?= $statusClass ?>">
                        <?= Html::encode(strtoupper($status)) ?>
                    </span>
                </div>

            </div>

        </div>
    </div>

    <!-- RECEIVED PRODUCTS -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">

            <div>
                <h5 class="mb-1">
                    <i class="bi bi-box-seam me-2 text-primary"></i>
                    Received Products
                </h5>

                <small class="text-muted">
                    Products and purchase costs included in this receipt.
                </small>
            </div>

            <span class="badge bg-primary">
                <?= count($items) ?> Product Lines
            </span>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Product Details</th>
                            <th>Supplier Product Code</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Unit Cost</th>
                            <th class="text-end pe-4">Total Amount</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (empty($items))
                        { ?>

                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="bi bi-inbox display-5 text-muted"></i>
                                    <p class="text-muted mt-3 mb-0">
                                        No products found in this receipt.
                                    </p>
                                </td>
                            </tr>

                        <?php }
                        else
                        {
                            foreach ($items as $index => $item)
                            {
                                $product = $item->product;
                                $supplierProduct = $item->supplierProduct;
                                ?>

                                <tr>

                                    <td class="ps-4 text-muted">
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            <?= Html::encode($product->name ?? 'Unknown Product') ?>
                                        </div>

                                        <?php if (!empty($product->model))
                                        { ?>
                                            <small class="text-muted">
                                                Model: <?= Html::encode($product->model) ?>
                                            </small>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <?php if (!empty($supplierProduct->supplier_product_code))
                                        { ?>
                                            <span class="badge bg-light text-dark border">
                                                <?= Html::encode($supplierProduct->supplier_product_code) ?>
                                            </span>
                                        <?php }
                                        else
                                        { ?>
                                            <span class="text-muted">—</span>
                                        <?php } ?>
                                    </td>

                                    <td class="text-end">
                                        <?= number_format((float) $item->qty,3) ?>
                                    </td>

                                    <td class="text-end">
                                        ₱<?= number_format((float) $item->amount,2) ?>
                                    </td>

                                    <td class="text-end pe-4 fw-semibold">
                                        ₱<?= number_format((float) $item->total_amount,2) ?>
                                    </td>

                                </tr>

                                <?php
                            }
                        } ?>

                    </tbody>

                    <tfoot class="table-light">

                        <tr>
                            <th colspan="3" class="text-end py-3">
                                GRAND TOTAL
                            </th>

                            <th class="text-end py-3">
                                <?= number_format($totalQuantity,3) ?>
                            </th>

                            <th></th>

                            <th class="text-end pe-4 py-3 text-primary fs-5">
                                ₱<?= number_format($grandTotal,2) ?>
                            </th>
                        </tr>

                    </tfoot>

                </table>

            </div>

        </div>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-box-seam fs-3"></i>
                    </div>

                    <div>
                        <div class="text-muted small">
                            Product Lines
                        </div>

                        <h4 class="fw-bold mb-0">
                            <?= number_format(count($items)) ?>
                        </h4>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-boxes fs-3"></i>
                    </div>

                    <div>
                        <div class="text-muted small">
                            Total Quantity
                        </div>

                        <h4 class="fw-bold mb-0">
                            <?= number_format($totalQuantity,3) ?>
                        </h4>
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
                        <div class="text-muted small">
                            Total Receipt Value
                        </div>

                        <h4 class="fw-bold mb-0">
                            ₱<?= number_format($grandTotal,2) ?>
                        </h4>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- INVENTORY INFORMATION -->
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-clipboard-check me-2 text-primary"></i>
                Inventory Information
            </h5>
        </div>

        <div class="card-body p-4">

            <div class="row g-3">

                <div class="col-md-6">
                    <div class="text-muted small mb-1">
                        Stock Availability
                    </div>

                    <?php if ($status === 'posted')
                    { ?>
                        <span class="text-success fw-semibold">
                            <i class="bi bi-check-circle me-1"></i>
                            Included in Inventory
                        </span>
                    <?php }
                    else
                    { ?>
                        <span class="text-warning fw-semibold">
                            <i class="bi bi-clock-history me-1"></i>
                            Not Available for Stock Allocation
                        </span>
                    <?php } ?>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small mb-1">
                        Inventory Costing Method
                    </div>

                    <div class="fw-semibold">
                        FIFO — First In, First Out
                    </div>
                </div>

            </div>

            <hr class="my-4">

            <div class="small text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Product costs are preserved per receipt item.
                When stock is issued, the FIFO allocation process
                uses the original cost of the corresponding Inventory IN batch.
            </div>

        </div>
    </div>

</div>
