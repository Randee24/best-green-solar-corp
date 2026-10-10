<?php

use yii\helpers\Html;

$this->title = 'Inventory OUT #' . $model->id;

$this->params['breadcrumbs'][] = [
    'label' => 'Inventory OUT',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

$colors = [
    'draft' => 'warning',
    'posted' => 'success',
    'reversed' => 'secondary',
];

$statusColor = $colors[$model->status] ?? 'secondary';

?>

<div class="inventory-out-view">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-box-arrow-up text-primary me-2"></i>
                OUT-<?= str_pad($model->id,5,'0',STR_PAD_LEFT) ?>
            </h3>

            <span class="badge bg-<?= $statusColor ?>">
                <?= Html::encode(ucfirst($model->status)) ?>
            </span>
        </div>

        <div class="d-flex gap-2">
            <?= Html::a('Back',['index'],[
                'class' => 'btn btn-outline-secondary',
            ]) ?>

            <?php if ($model->status === 'draft')
            { ?>
                <?= Html::a(
                    '<i class="bi bi-pencil me-1"></i> Edit',
                    ['update','id' => $model->id],
                    ['class' => 'btn btn-outline-primary']
                ) ?>

                <?= Html::beginForm(['post','id' => $model->id],'post') ?>
                    <?= Html::submitButton(
                        '<i class="bi bi-check-circle me-1"></i> Post Inventory',
                        [
                            'class' => 'btn btn-success',
                            'data-confirm' => 'Post this transaction and deduct stock using FIFO?',
                        ]
                    ) ?>
                <?= Html::endForm() ?>
            <?php } ?>
        </div>

    </div>

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Transaction Information</h5>
        </div>

        <div class="card-body p-4">

            <div class="row g-4">

                <div class="col-md-3">
                    <div class="text-muted small">Project</div>
                    <div class="fw-semibold">
                        <?= Html::encode($model->project->name ?? 'N/A') ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="text-muted small">Transaction Date</div>
                    <div class="fw-semibold">
                        <?= Yii::$app->formatter->asDate($model->transaction_date) ?>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="text-muted small">Date & Time</div>
                    <div class="fw-semibold">
                        <?= Yii::$app->formatter->asDatetime($model->date_and_time) ?>
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

            </div>

        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-box-seam text-primary me-2"></i>
                Released Products
            </h5>
        </div>

        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-1">id</th>
                            <th class="ps-2">Product</th>
                            <th class="text-end">Qty</th>
                            <th>Remarks</th>
                            <th class="text-end pe-3">FIFO Cost</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($model->inventoryOutItems as $item)
                        { ?>
                            <tr>
								 <td class="ps-1">
                                    <?= Html::encode($item->product->id ?? 'Unknown Product') ?>
                                </td>
                                <td class="ps-2">
                                    <?= Html::encode($item->product->name ?? 'Unknown Product') ?>
                                </td>

                                <td class="text-end">
                                    <?= number_format($item->qty) ?>
                                </td>

                                <td>
                                    <?= nl2br(Html::encode($item->remarks ?: '—')) ?>
                                </td>

                                <td class="text-end pe-3">
                                    <?= $model->status === 'draft'
                                        ? 'Pending'
                                        : '₱' . number_format((float) $item->getFifoCost(),2) ?>
                                </td>
                            </tr>

                            <?php if ($model->status === 'posted' && !empty($item->allocations))
                            { ?>
                                <tr class="table-light">
                                    <td colspan="4" class="px-4 py-3">
                                        <div class="small fw-semibold mb-2">
                                            FIFO Allocations
                                        </div>

                                        <table class="table table-sm table-bordered bg-white mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Inventory IN Batch</th>
                                                    <th class="text-end">Qty</th>
                                                    <th class="text-end">Unit Cost</th>
                                                    <th class="text-end">Total Cost</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($item->allocations as $allocation)
                                                { ?>
                                                    <tr>
                                                        <td>
															<?php
																$inventoryInItem = $allocation->inventoryInItem;
																$inventoryInId = $inventoryInItem->inventory_in_id ?? null;
															?>

															<?php if ($inventoryInId)
															{ ?>
																<?= Html::a(
																	'IN-' . str_pad($inventoryInId,5,'0',STR_PAD_LEFT),
																	['/inventory-in/view','id' => $inventoryInId],
																	[
																		'class' => 'fw-semibold text-decoration-none',
																		'title' => 'View Inventory IN #' . $inventoryInId,
																	]
																) ?>
															<?php }
															else
															{ ?>
																<span class="text-muted">N/A</span>
															<?php } ?>
														</td>
                                                        <td class="text-end">
                                                            <?= number_format((float) $allocation->qty,3) ?>
                                                        </td>
                                                        <td class="text-end">
                                                            ₱<?= number_format((float) $allocation->amount,2) ?>
                                                        </td>
                                                        <td class="text-end">
                                                            ₱<?= number_format((float) $allocation->total_amount,2) ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php } ?>

                        <?php } ?>

                    </tbody>

                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">Total FIFO Cost</th>
                            <th class="text-end pe-3">
                                <?= $model->status === 'draft'
                                    ? 'Pending'
                                    : '₱' . number_format((float) $model->getTotalFifoCost(),2) ?>
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>

        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">
        <h5 class="mb-0">
            <i class="bi bi-clock-history text-primary me-2"></i>
            Change History
        </h5>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Previous Value</th>
                        <th>New Value</th>
                        <th>Changed By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($model->inventoryOutChanges as $change)
                    { ?>
                        <tr>
                            <td class="ps-3 text-nowrap">
                                <?= Yii::$app->formatter->asDatetime($change->changed_at) ?>
                            </td>

                            <td>
                                <span class="text-danger">
                                    <?= Html::encode($change->old_value) ?>
                                </span>
                            </td>

                            <td>
                                <span class="text-success">
                                    <?= Html::encode($change->new_value) ?>
                                </span>
                            </td>

                            <td>
                                <?= Html::encode($change->user->username ?? 'System') ?>
                            </td>
                        </tr>
                    <?php } ?>

                    <?php if (empty($model->inventoryOutChanges))
                    { ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No changes recorded.
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</div>