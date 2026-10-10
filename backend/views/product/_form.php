
<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\ProductTypes;
use backend\models\Units;

/** @var yii\web\View $this */
/** @var backend\models\Product $model */

$productTypes = ArrayHelper::map(
    ProductTypes::find()->orderBy(['name' => SORT_ASC])->all(),
    'id',
    'name'
);

$units = ArrayHelper::map(
    Units::find()->orderBy(['name' => SORT_ASC])->all(),
    'id',
    'name'
);

?>

<div class="product-form">

    <?php $form = ActiveForm::begin([
        'id' => 'product-form',
        'options' => [
            'autocomplete' => 'off',
        ],
    ]); ?>

    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-box-seam text-primary me-2"></i>
                Product Information
            </h5>
        </div>

        <div class="card-body p-4">

            <?= $form->errorSummary($model,[
                'class' => 'alert alert-danger',
                'header' => '<strong>Please correct the following errors:</strong>',
            ]) ?>

            <div class="row g-3">

                <div class="col-md-8">
                    <?= $form->field($model,'name')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Enter product name',
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model,'model')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'Enter product model',
                    ]) ?>
                </div>

                <div class="col-12">
                    <?= $form->field($model,'description')->textarea([
                        'rows' => 3,
                        'maxlength' => true,
                        'placeholder' => 'Enter product description',
                    ]) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model,'product_type_id')->dropDownList(
                        $productTypes,
                        [
                            'prompt' => 'Select Product Type',
                            'class' => 'form-select',
                        ]
                    ) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model,'unit_id')->dropDownList(
                        $units,
                        [
                            'prompt' => 'Select Unit',
                            'class' => 'form-select',
                        ]
                    ) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model,'installation_price')->input('number',[
                        'min' => 0,
                        'step' => '0.01',
                        'placeholder' => '0.00',
                    ]) ?>
                </div>

            </div>

        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">

        <?= Html::a(
            '<i class="bi bi-x-circle me-1"></i> Cancel',
            ['index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>

        <?= Html::submitButton(
            $model->isNewRecord
                ? '<i class="bi bi-check-circle me-1"></i> Save Product'
                : '<i class="bi bi-save me-1"></i> Update Product',
            [
                'class' => $model->isNewRecord
                    ? 'btn btn-primary'
                    : 'btn btn-success',
            ]
        ) ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
