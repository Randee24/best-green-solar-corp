<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Pages $model */

$this->title = 'Create Page';
$this->params['breadcrumbs'][] = ['label' => 'Pages Management','url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="pages-create">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-plus-circle text-primary me-2"></i>
                <?= Html::encode($this->title) ?>
            </h3>
            <div class="text-muted small">Add a new page to the permissions directory.</div>
        </div>
        <?= Html::a(
            '<i class="bi bi-arrow-left me-1"></i> Back',
            ['index'],
            ['class' => 'btn btn-outline-secondary']
        ) ?>
    </div>

    <?= $this->render('_form',['model' => $model]) ?>
</div>
