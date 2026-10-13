
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var backend\models\search\ProductsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="products-search">

        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>

        <div class="row g-3 align-items-end">

            <div class="col-md-3">
                <?= $form->field($model, 'name')
                    ->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'Search Product Name',
                    ])
                    ->label('Product Name') ?>
            </div>

            <div class="col-md-3">
                <?= $form->field($model, 'description')
                    ->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'Search Description',
                    ])
                    ->label('Description') ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'model')
                    ->textInput([
                        'class' => 'form-control',
                        'placeholder' => 'Search Model',
                    ])
                    ->label('Model') ?>
            </div>

            
			<div class="col-md-2">
				<?= $form->field($model, 'product_type_id')
					->dropDownList($productTypeOptions,[
						'prompt' => 'All Product Types',
						'class' => 'form-select',
					])
					->label('Product Type') ?>
			</div>


            <div class="col-md-2">

                    <?= Html::submitButton(
                        '<i class="bi bi-search"></i> Search',
                        ['class' => 'btn btn-primary']
                    ) ?>

                    <?= Html::a(
                        '<i class="bi bi-arrow-clockwise"></i>',
                        ['index'],
                        [
                            'class' => 'btn btn-outline-secondary',
                            'title' => 'Reset Filters',
                        ]
                    ) ?>

            </div>

        </div>

        <?php ActiveForm::end(); ?>

</div>
