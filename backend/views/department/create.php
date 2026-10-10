
<?php

use yii\helpers\Html;

$this->title = 'Create Department';
$this->params['breadcrumbs'][] = [
    'label' => 'Department',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="department-create">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-plus-square text-primary me-2"></i>
                Create Department
            </h3>
            <p class="text-muted mb-0">
                Add a new department to the system.
            </p>
        </div>

        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i> Back',
            ['index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
    </div>

    <?= $this->render('_form',[
        'model' => $model,
    ]) ?>

</div>
