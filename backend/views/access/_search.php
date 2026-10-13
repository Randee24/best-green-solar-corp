
<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var backend\models\search\AccessSearch $searchModel */
/** @var array $roleOptions */
/** @var array $departmentOptions */
/** @var array $pageOptions */

?>

<div class="access-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <?= $form->field($searchModel,'role_id')->dropDownList($roleOptions,[
                'prompt' => 'All Roles',
                'class' => 'form-select',
            ])->label('Role') ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($searchModel,'department_id')->dropDownList($departmentOptions,[
                'prompt' => 'All Departments',
                'class' => 'form-select',
            ])->label('Department') ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($searchModel,'page_id')->dropDownList($pageOptions,[
                'prompt' => 'All Pages',
                'class' => 'form-select',
            ])->label('Page') ?>
        </div>

        <div class="col-md-2">
            <?= $form->field($searchModel,'status')->dropDownList([
                1 => 'Active',
                0 => 'Inactive',
            ],[
                'prompt' => 'All Status',
                'class' => 'form-select',
            ])->label('Status') ?>
        </div>

        <div class="col-md-2">
            <div class="d-flex gap-2 mb-3">
                <?= Html::submitButton('<i class="bi bi-search"></i> Search',[
                    'class' => 'btn btn-primary',
                ]) ?>

                <?= Html::a('<i class="bi bi-arrow-clockwise"></i>',['index'],[
                    'class' => 'btn btn-outline-secondary',
                    'title' => 'Reset Filters',
                ]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
