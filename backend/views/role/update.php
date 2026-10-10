
<?php

use yii\helpers\Html;

$this->title = 'Update Role #' . $model->id;
$this->params['breadcrumbs'][] = [
    'label' => 'Roles',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = [
    'label' => $model->name,
    'url' => ['view','id' => $model->id],
];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="role-update">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-pencil-square text-primary me-2"></i>
                Update Role
            </h3>
            <p class="text-muted mb-0">
                Editing <?= Html::encode($model->name) ?>
            </p>
        </div>

        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i> Back',
            ['view','id' => $model->id],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
    </div>

    <?= $this->render('_form',[
        'model' => $model,
    ]) ?>

</div>
