<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\InventoryIn $model */

$isUpdate = !$model->isNewRecord;
?>

<div class="inventory-in-form">

    <?php $form = ActiveForm::begin([
        'id' => 'inventory-in-form',
        'options' => ['class' => 'needs-validation'],
        'fieldConfig' => [
            'options' => ['class' => 'mb-3'],
            'labelOptions' => ['class' => 'form-label fw-semibold'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

    <!-- Receipt Information -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-1">
                <i class="bi bi-box-arrow-in-down me-2"></i>
                Inventory Receipt
            </h5>
            <small class="text-muted">
                Record the supplier and products received.
            </small>
        </div>

        <div class="card-body p-4">
            <div class="row">

                <div class="col-md-6">
                    <?= $form->field($model, 'transaction_date')
                        ->input('date', [
                            'class' => 'form-control',
                            'value' => $model->transaction_date
                                ? date('Y-m-d', strtotime($model->transaction_date))
                                : date('Y-m-d'),
                        ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'supplier_id')
                        ->dropDownList($suppliers, [
                            'prompt' => 'Select supplier',
                            'class' => 'form-select',
                        ]) ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Received Products -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3
                    d-flex justify-content-between align-items-center">

            <div>
                <h5 class="mb-1">
                    <i class="bi bi-list-ul me-2"></i>
                    Received Products
                </h5>
                <small class="text-muted">
                    Add each product and its actual purchase cost.
                </small>
            </div>

            <button type="button"
                    class="btn btn-outline-primary btn-sm"
                    id="add-item">
                <i class="bi bi-plus-circle me-1"></i>
                Add Product
            </button>

        </div>

        <div class="card-body p-3">

            <div class="table-responsive">
                <table class="table table-bordered align-middle"
                       id="items-table">

                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 220px;">Product</th>
                            <th style="min-width: 110px;">Quantity</th>
                            <th style="min-width: 150px;">Unit Cost</th>
                            <th style="min-width: 150px;">Total Amount</th>
                            <th class="text-center" style="width: 70px;">
                                Action
                            </th>
                        </tr>
                    </thead>

                    
					<tbody id="items-body">

						<?php foreach ($items as $index => $item): ?>

							<tr class="item-row">

								<!-- Product -->
								<td>
									<?= Html::hiddenInput(
										"items[$index][id]",
										$item['id'] ?? ''
									) ?>

									<?= Html::dropDownList(
										"items[$index][product_id]",
										$item['product_id'] ?? '',
										$products,
										[
											'class' => 'form-select product-select',
											'prompt' => 'Select product',
											'required' => true,
										]
									) ?>
								</td>

								<!-- Quantity -->
								<td>
									<?= Html::input(
										'number',
										"items[$index][qty]",
										$item['qty'] ?? 1,
										[
											'class' => 'form-control qty-input',
											'min' => '0.001',
											'step' => '0.001',
											'required' => true,
										]
									) ?>
								</td>

								<!-- Unit Cost -->
								<td>
									<?= Html::input(
										'number',
										"items[$index][amount]",
										$item['amount'] ?? '0.00',
										[
											'class' => 'form-control amount-input',
											'min' => '0',
											'step' => '0.01',
											'required' => true,
										]
									) ?>
								</td>

								<!-- Total Amount -->
								<td>
									<input type="number"
										   class="form-control total-input bg-light"
										   value="0.00"
										   readonly>
								</td>

								<!-- Action -->
								<td class="text-center">
									<button type="button"
											class="btn btn-outline-danger btn-sm remove-item"
											title="Remove product">
										<i class="bi bi-trash"></i>
									</button>
								</td>

							</tr>

						<?php endforeach; ?>

					</tbody>


                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">
                                Receipt Total
                            </th>
                            <th>
                                <input type="text"
                                       id="grand-total"
                                       class="form-control fw-bold text-end"
                                       value="0.00"
                                       readonly>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>

                </table>
            </div>

            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Each product's unit cost is saved with its receipt line
                for FIFO inventory costing.
            </div>

        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex justify-content-end gap-2">

        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i> Cancel',
            ['index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>

        <?= Html::submitButton(
            '<i class="bi bi-save me-1"></i> Save Inventory IN',
            ['class' => 'btn btn-primary']
        ) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- Hidden template for additional rows -->
<template id="item-template">
    <tr class="item-row">

        <td>
            <select name="items[__INDEX__][product_id]"
                    class="form-select product-select"
                    required>
                <option value="">Select product</option>
                <?php foreach ($products as $id => $name): ?>
                    <option value="<?= Html::encode($id) ?>">
                        <?= Html::encode($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>

        <td>
            <input type="number"
                   name="items[__INDEX__][qty]"
                   class="form-control qty-input"
                   min="0.001"
                   step="0.001"
                   value="1"
                   required>
        </td>

        <td>
            <input type="number"
                   name="items[__INDEX__][amount]"
                   class="form-control amount-input"
                   min="0"
                   step="0.01"
                   value="0.00"
                   required>
        </td>

        <td>
            <input type="number"
                   class="form-control total-input bg-light"
                   value="0.00"
                   readonly>
        </td>

        <td class="text-center">
            <button type="button"
                    class="btn btn-outline-danger btn-sm remove-item">
                <i class="bi bi-trash"></i>
            </button>
        </td>

    </tr>
</template>

<?php
$js = <<<'JS'

let nextItemIndex = document.querySelectorAll('#items-body .item-row').length;

function calculateRow(row) {
    const qty = parseFloat(
        row.querySelector('.qty-input').value
    ) || 0;

    const amount = parseFloat(
        row.querySelector('.amount-input').value
    ) || 0;

    const total = qty * amount;

    row.querySelector('.total-input').value = total.toFixed(2);

    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grandTotal = 0;

    document.querySelectorAll('#items-body .item-row').forEach(row => {
        grandTotal += parseFloat(
            row.querySelector('.total-input').value
        ) || 0;
    });

    document.getElementById('grand-total').value =
        grandTotal.toFixed(2);
}

document.getElementById('add-item').addEventListener('click', function () {
    const template = document.getElementById('item-template').innerHTML;
    const html = template.replace(/__INDEX__/g, nextItemIndex++);

    document.getElementById('items-body').insertAdjacentHTML(
        'beforeend',
        html
    );

    calculateRow(
        document.querySelector('#items-body .item-row:last-child')
    );
});

document.getElementById('items-body').addEventListener('input', function (e) {
    if (
        e.target.classList.contains('qty-input') ||
        e.target.classList.contains('amount-input')
    ) {
        calculateRow(e.target.closest('.item-row'));
    }
});

document.getElementById('items-body').addEventListener('click', function (e) {
    const button = e.target.closest('.remove-item');

    if (!button) {
        return;
    }

    const rows = document.querySelectorAll('#items-body .item-row');

    if (rows.length <= 1) {
        alert('At least one product row is required.');
        return;
    }

    button.closest('.item-row').remove();
    calculateGrandTotal();
});

// Initialize totals for rows loaded from POST data.
document.querySelectorAll('#items-body .item-row').forEach(calculateRow);

JS;

$this->registerJs($js);
?>
