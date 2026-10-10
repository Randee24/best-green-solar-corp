
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;

/** @var yii\web\View $this */
/** @var backend\models\Products $model */

$this->title = $model->name;

$this->params['breadcrumbs'][] = [
    'label' => 'Products',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

$qty = $model->getAvailableQty();

$historyProvider = new ArrayDataProvider([
    'allModels' => $model->productPriceChanges,
    'pagination' => [
        'pageSize' => 10,
    ],
    'sort' => false,
]);

?>

<div class="product-view">
<?= $this->render('//layouts/_flash') ?>
    <!-- HEADER -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-box-seam text-primary me-2"></i>
                <?= Html::encode($model->name) ?>
            </h3>

            <div class="text-muted">
                Product ID: #<?= Html::encode($model->id) ?>
            </div>
        </div>

        <div class="d-flex gap-2">

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

    <!-- SUMMARY -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-boxes fs-3"></i>
                    </div>

                    <div>
                        <div class="text-muted small">
                            Available Qty
                        </div>

                        <h4 class="fw-bold mb-0 <?= $qty > 0 ? 'text-success' : 'text-danger' ?>">
                            <?= number_format($qty,3) ?>
                        </h4>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-cash-stack fs-3"></i>
                    </div>

                    <div>
                        <div class="text-muted small">
                            Installation Price
                        </div>

                        <h4 class="fw-bold mb-0">
                            ₱<?= number_format((float) $model->installation_price,2) ?>
                        </h4>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">

                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="bi bi-calendar-check fs-3"></i>
                    </div>

                    <div>
                        <div class="text-muted small">
                            Date Added
                        </div>

                        <h5 class="fw-bold mb-0">
                            <?= Yii::$app->formatter->asDate(
                                $model->created_at,
                                'php:M d, Y'
                            ) ?>
                        </h5>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- PRODUCT DETAILS -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-info-circle text-primary me-2"></i>
                Product Details
            </h5>
        </div>

        <div class="card-body p-4">

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="text-muted small mb-1">Model</div>

                    <div class="fw-semibold">
                        <?= Html::encode($model->model ?: 'N/A') ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small mb-1">Product Type</div>

                    <div class="fw-semibold">
                        <?= Html::encode($model->productType->name ?? 'N/A') ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="text-muted small mb-1">Unit</div>

                    <div class="fw-semibold">
                        <?= Html::encode($model->unit->name ?? 'N/A') ?>
                    </div>
                </div>

                <div class="col-12">
                    <div class="text-muted small mb-1">Description</div>

                    <div>
                        <?= nl2br(Html::encode($model->description)) ?>
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
                <?= count($model->productPriceChanges) ?> Changes
            </span>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <?= GridView::widget([
                    'dataProvider' => $historyProvider,
                    'layout' => "{items}\n<div class=\"px-3 py-2\">{summary}{pager}</div>",
                    'emptyText' => 'No supplier price changes recorded for this product.',
                    'tableOptions' => [
                        'class' => 'table table-hover align-middle mb-0',
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
                            'contentOptions' => [
                                'class' => 'ps-3',
                                'style' => 'white-space:nowrap;',
                            ],
                        ],

                        [
                            'label' => 'Supplier',
                            'format' => 'raw',
                            'value' => function($history)
                            {
                                $supplier = $history->supplierProduct->supplier ?? null;

                                if ($supplier === null)
                                {
                                    return '<span class="text-muted">N/A</span>';
                                }

                                return Html::encode($supplier->name);
                            },
                        ],

                        [
                            'label' => 'Supplier Code',
                            'value' => function($history)
                            {
                                return $history->supplierProduct->supplier_product_code ?? '—';
                            },
                        ],

                        [
                            'label' => 'Old Price',
                            'format' => 'raw',
                            'value' => function($history)
                            {
                                return '₱' . number_format((float) $history->old_price,2);
                            },
                            'contentOptions' => [
                                'class' => 'text-end',
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
                            'contentOptions' => [
                                'class' => 'text-end',
                            ],
                        ],

                        [
                            'label' => 'Difference',
                            'format' => 'raw',
                            'value' => function($history)
                            {
                                $difference = (float) $history->new_price
                                    - (float) $history->old_price;

                                if ($difference > 0)
                                {
                                    return '<span class="text-danger fw-semibold">'
                                        . '<i class="bi bi-arrow-up"></i> ₱'
                                        . number_format(abs($difference),2)
                                        . '</span>';
                                }
                                elseif ($difference < 0)
                                {
                                    return '<span class="text-success fw-semibold">'
                                        . '<i class="bi bi-arrow-down"></i> ₱'
                                        . number_format(abs($difference),2)
                                        . '</span>';
                                }

                                return '<span class="text-muted">No Change</span>';
                            },
                            'contentOptions' => [
                                'class' => 'text-end',
                            ],
                        ],

                        [
                            'label' => 'Changed By',
                            'value' => function($history)
                            {
                                return $history->user->username ?? 'System';
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
