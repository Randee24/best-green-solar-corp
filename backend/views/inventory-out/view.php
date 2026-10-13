<?php

use yii\helpers\Html;
use yii\bootstrap5\BootstrapPluginAsset;

/**
 * @var yii\web\View $this
 * @var backend\models\InventoryOuts $model
 */

BootstrapPluginAsset::register($this);

$this->title = 'Inventory OUT #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Inventory OUT','url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$items = $model->inventoryOutItems;
$changes = $model->inventoryOutChanges;
$totalQty = 0;
foreach ($items as $item)
{
    $totalQty += (float) $item->qty;
}

$statusColor = match ($model->status)
{
    'draft' => 'warning',
    'posted' => 'success',
    'reversed' => 'secondary',
    default => 'secondary',
};
$isDraft = $model->status === 'draft';
?>

<div class="inventory-out-view">
    <?= $this->render('//layouts/_flash') ?>

    <!-- HEADER -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h3 class="mb-1">
                <?= Html::encode($this->title) ?>
                <span class="badge bg-<?= $statusColor ?> fs-6 align-middle ms-2">
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
                    'type' => 'button',
                    'data-bs-toggle' => 'offcanvas',
                    'data-bs-target' => '#inventoryOutHistory',
                    'aria-controls' => 'inventoryOutHistory',
                ]
            ) ?>

            <?php if ($isDraft): ?>
                <?= Html::a(
                    '<i class="bi bi-pencil me-1"></i> Edit',
                    ['update','id' => $model->id],
                    ['class' => 'btn btn-outline-primary']
                ) ?>

                <?= Html::button(
                    '<i class="bi bi-check-circle me-1"></i> Post Inventory OUT',
                    [
                        'class' => 'btn btn-success',
                        'type' => 'button',
                        'data-bs-toggle' => 'modal',
                        'data-bs-target' => '#postInventoryOutModal',
                    ]
                ) ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- COMPACT TRANSACTION SUMMARY -->
    <div class="card shadow-sm mb-3">
        <div class="card-body py-3">
            <div class="row g-3 align-items-center">
                <div class="col-md-3">
                    <div class="text-muted small">Project</div>
                    <div class="fw-semibold"><?= Html::encode($model->project->name ?? 'N/A') ?></div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted small">Date &amp; Time</div>
                    <div class="fw-semibold">
                        <?= $model->date_and_time
                            ? Yii::$app->formatter->asDatetime($model->date_and_time)
                            : 'N/A' ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Inventory Period</div>
                    <div class="fw-semibold">
                        <?= $model->inventoryPeriod
                            ? Html::encode($model->inventoryPeriod->getPeriodName())
                            : 'N/A' ?>
                    </div>
                </div>
                <div class="col-6 col-md-1">
                    <div class="text-muted small">Products</div>
                    <div class="fw-semibold"><?= number_format(count($items)) ?></div>
                </div>
                <div class="col-6 col-md-1">
                    <div class="text-muted small">Total Qty</div>
                    <div class="fw-semibold"><?= number_format($totalQty) ?></div>
                </div>
                <div class="col-md-2">
                    <div class="text-muted small">Total Cost</div>
                    <div class="fw-semibold text-danger">
                        <?= $isDraft ? 'Pending' : '₱' . number_format((float) $model->getTotalFifoCost(),2) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FULL-WIDTH RELEASED PRODUCTS TABLE -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i> Released Products</h5>
            <span class="badge bg-secondary"><?= number_format(count($items)) ?> Items</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 55px;">#</th>
                            <th>Product</th>
                            <th class="text-end text-nowrap">Quantity</th>
                            <th>Remarks</th>
                            <th class="text-end text-nowrap">Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <div class="fw-semibold">
                                        <?= Html::encode($item->product->name ?? 'Unknown Product') ?>
                                    </div>
                                    <?php if (!empty($item->product->model)): ?>
                                        <div class="small text-muted">
                                            <?= Html::encode($item->product->model) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="small text-muted">
                                        Product ID: #<?= Html::encode($item->product_id) ?>
                                    </div>
                                </td>
                                <td class="text-end text-nowrap">
                                    <?= number_format($item->qty) ?>
                                    
                                </td>
                                <td><?= nl2br(Html::encode($item->remarks ?: '—')) ?></td>
                                <td class="text-end fw-semibold text-nowrap">
                                    <?= $isDraft ? 'Pending' : '₱' . number_format((float) $item->getFifoCost(),2) ?>
                                </td>
                            </tr>

                            <?php if ($model->status === 'posted' && !empty($item->allocations)): ?>
                                <tr class="table-light">
                                    <td></td>
                                    <td colspan="4" class="px-3 py-3">
                                        <div class="small fw-semibold mb-2">
                                            <i class="bi bi-diagram-3 me-1"></i> Allocations
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered bg-white mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Inventory IN ID</th>
                                                        <th class="text-end">Qty</th>
                                                        <th class="text-end">Unit Cost</th>
                                                        <th class="text-end">Total Cost</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($item->allocations as $allocation): ?>
                                                        <?php
                                                        $inventoryInId = $allocation->inventoryInItem->inventory_in_id ?? null;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php if ($inventoryInId): ?>
                                                                    <?= Html::a(
                                                                        'IN-' . str_pad($inventoryInId,5,'0',STR_PAD_LEFT),
                                                                        ['/inventory-in/view','id' => $inventoryInId],
                                                                        [
                                                                            'class' => 'fw-semibold text-decoration-none',
                                                                            'title' => 'View Inventory IN #' . $inventoryInId,
                                                                        ]
                                                                    ) ?>
                                                                <?php else: ?>
                                                                    <span class="text-muted">N/A</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-end"><?= number_format($allocation->qty) ?></td>
                                                            <td class="text-end">₱<?= number_format((float) $allocation->amount,2) ?></td>
                                                            <td class="text-end">₱<?= number_format((float) $allocation->total_amount,2) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (empty($items)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No released products recorded.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2" class="text-end">Totals</th>
                            <th class="text-end"><?= number_format($totalQty) ?></th>
                            <th></th>
                            <th class="text-end text-nowrap">
                                <?= $isDraft ? 'Pending' : '₱' . number_format((float) $model->getTotalFifoCost(),2) ?>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- POST INVENTORY OUT CONFIRMATION MODAL -->
<?php if ($isDraft): ?>
    <div class="modal fade" id="postInventoryOutModal" tabindex="-1"
         aria-labelledby="postInventoryOutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <?= Html::beginForm(['post','id' => $model->id],'post') ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="postInventoryOutModalLabel">
                            <i class="bi bi-check-circle me-2"></i> Confirm Inventory OUT Posting
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">
                            Post <strong><?= Html::encode($this->title) ?></strong>?
                        </p>
                        <div class="alert alert-warning small mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Posting deducts available stock using FIFO allocations.
                            Verify the released products before continuing.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?= Html::button('Cancel',[
                            'class' => 'btn btn-outline-secondary',
                            'data-bs-dismiss' => 'modal',
                        ]) ?>
                        <?= Html::submitButton(
                            '<i class="bi bi-check-circle me-1"></i> Confirm Posting',
                            ['class' => 'btn btn-success']
                        ) ?>
                    </div>
                <?= Html::endForm() ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- CHANGE HISTORY SIDEBAR -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="inventoryOutHistory"
     aria-labelledby="inventoryOutHistoryLabel" style="--bs-offcanvas-width: 420px;">
    <div class="offcanvas-header border-bottom">
        <div>
            <h5 class="offcanvas-title mb-1" id="inventoryOutHistoryLabel">
                <i class="bi bi-clock-history me-2"></i> Inventory OUT History
            </h5>
            <div class="small text-muted"><?= Html::encode($this->title) ?></div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php if (empty($changes)): ?>
            <div class="text-muted text-center py-4">
                <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                No changes recorded.
            </div>
        <?php else: ?>
            <div class="border-start ps-3">
                <?php foreach ($changes as $change): ?>
                    <?php
                    $oldValue = (string) ($change->old_value ?? '');
                    $newValue = (string) ($change->new_value ?? '');
                    $isPosting = stripos($newValue,'posted') !== false;
                    ?>
                    <div class="mb-4">
                        <div class="fw-semibold mb-1">
                            <i class="bi <?= $isPosting ? 'bi-check-circle text-success' : 'bi-pencil-square text-warning' ?> me-1"></i>
                            <?= $isPosting ? 'Inventory Posted' : 'Inventory Updated' ?>
                        </div>
                        <div class="small text-muted mb-2">
                            <?= $change->changed_at
                                ? Yii::$app->formatter->asDatetime($change->changed_at)
                                : 'N/A' ?>
                        </div>
                        <div class="border rounded p-2 bg-light mb-2">
                            <div class="small text-muted mb-1">Previous Value</div>
                            <div class="small text-danger mb-2">
                                <?= Html::encode($oldValue !== '' ? $oldValue : '—') ?>
                            </div>
                            <div class="small text-muted mb-1">New Value</div>
                            <div class="small text-success">
                                <?= Html::encode($newValue !== '' ? $newValue : '—') ?>
                            </div>
                        </div>
                        <div class="small text-muted">
                            <i class="bi bi-person me-1"></i>
                            <?= Html::encode($change->user->username ?? 'System') ?>
                            <?php if (!empty($change->user->department->name)): ?>
                                (<?= Html::encode($change->user->department->name) ?>)
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
