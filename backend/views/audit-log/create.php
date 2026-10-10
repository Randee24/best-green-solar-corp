<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\AuditLogs $model */

$this->title = 'Create Audit Logs';
$this->params['breadcrumbs'][] = ['label' => 'Audit Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="audit-logs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
