<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use backend\models\Products;
use backend\models\Projects;

/** @var backend\models\InventoryOuts $model */
/** @var array $items */

$projects = ArrayHelper::map(
    Projects::find()->orderBy(['name' => SORT_ASC])->all(),
    'id',
    'name'
);

$products = Products::getProductsWithStock();

if (!$model->isNewRecord)
{
    foreach ($items as $item)
    {
        $productId = (int) ($item['product_id'] ?? 0);

        if ($productId > 0 && !isset($products[$productId]))
        {
            $product = Products::findOne($productId);

            if ($product !== null)
            {
                $products[$productId] = $product->name .
                    ' (Available: ' .
                    number_format($product->getAvailableQty(),3) . ')';
            }
        }
    }
}

if (empty($items))
{
    $items = [['product_id' => '','qty' => 1,'remarks' => '']];
}

$productOptions = Html::renderSelectOptions(null,$products);

?>

<div class="inventory-out-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model,[
        'class' => 'alert alert-danger',
    ]) ?>

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-box-arrow-up me-2 text-primary"></i>
                Inventory OUT Information
            </h5>
        </div>

        <div class="card-body p-4">
            <div class="row g-3">

                <div class="col-md-4">
                    <?= $form->field($model,'date_and_time')->textInput([
                        'type' => 'datetime-local',
                        'value' => str_replace(' ','T',substr($model->date_and_time,0,16)),
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model,'transaction_date')->input('date') ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model,'project_id')->dropDownList(
                        $projects,
                        ['prompt' => 'Select Project']
                    ) ?>
                </div>

            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">

            <h5 class="mb-0">
                <i class="bi bi-box-seam me-2 text-primary"></i>
                Products
            </h5>

            <button type="button" id="add-product" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-circle me-1"></i> Add Product
            </button>

        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered align-middle" id="items-table">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:250px">Product</th>
                            <th style="width:130px">Qty</th>
                            <th>Remarks</th>
                            <th style="width:65px"></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($items as $index => $item)
                        { ?>
                            <tr>
                                <td>
                                    <?= Html::dropDownList(
                                        "items[$index][product_id]",
                                        $item['product_id'] ?? null,
                                        $products,
                                        [
                                            'class' => 'form-select',
                                            'prompt' => 'Select Product',
                                            'required' => true,
                                        ]
                                    ) ?>
                                </td>

                                <td>
                                    <?= Html::input(
                                        'number',
                                        "items[$index][qty]",
                                        $item['qty'] ?? 1,
                                        [
                                            'class' => 'form-control',
                                            'min' => 1,
                                            'step' => 1,
                                            'required' => true,
                                        ]
                                    ) ?>
                                </td>

                                <td>
                                    <?= Html::textInput(
                                        "items[$index][remarks]",
                                        $item['remarks'] ?? '',
                                        ['class' => 'form-control']
                                    ) ?>
                                </td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">

        <?= Html::a('Cancel',['index'],[
            'class' => 'btn btn-outline-secondary',
        ]) ?>

        <?= Html::submitButton(
            $model->isNewRecord ? 'Create Inventory OUT' : 'Update Inventory OUT',
            ['class' => 'btn btn-primary']
        ) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$optionsJson = Json::htmlEncode($productOptions);
$nextIndex = empty($items)
    ? 0
    : max(array_map('intval',array_keys($items))) + 1;

$js = <<<JS
(function()
{
    const table = document.querySelector('#items-table tbody');
    const productOptions = {$optionsJson};
    let index = {$nextIndex};

    document.querySelector('#add-product').addEventListener('click',function()
    {
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>
                <select name="items[\${index}][product_id]" class="form-select" required>
                    <option value="">Select Product</option>
                    \${productOptions}
                </select>
            </td>
            <td>
                <input type="number" name="items[\${index}][qty]"
                       value="1" min="1" step="1" required class="form-control">
            </td>
            <td>
                <input type="text" name="items[\${index}][remarks]" class="form-control">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;

        table.appendChild(row);
        index++;
    });

    table.addEventListener('click',function(event)
    {
        const button = event.target.closest('.remove-item');

        if (button)
        {
            button.closest('tr').remove();
        }
    });
})();
JS;

$this->registerJs($js);

?>