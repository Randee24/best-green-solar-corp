
<?php

use yii\helpers\Html;

$this->title = 'Create Role';
$this->params['breadcrumbs'][] = [
    'label' => 'Roles',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="role-create">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-plus-square text-primary me-2"></i>
                Create Role
            </h3>
            <p class="text-muted mb-0">
                Add a new Role to the system.
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
