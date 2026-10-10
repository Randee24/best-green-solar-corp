
<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Projects $model */

$this->title = 'Create Project';
$this->params['breadcrumbs'][] = [
    'label' => 'Projects',
    'url' => ['index'],
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="project-create">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-plus-square text-primary me-2"></i>
                Create Project
            </h3>
            <p class="text-muted mb-0">
                Add a new project for inventory transactions.
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
