
<?php

use yii\helpers\Html;

$this->title = 'Create Unit';
$this->params['breadcrumbs'][] = [
    'label' => 'Units',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="unit-create">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-plus-square text-primary me-2"></i>
                Create Unit
            </h3>
            <p class="text-muted mb-0">
                Add a new unit to the system.
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
