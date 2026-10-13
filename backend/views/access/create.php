<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var backend\models\Accesses $model */
/** @var array $roleOptions */
/** @var array $departmentOptions */
/** @var array $pageOptions */
$this->title = 'Create Access';
$this->params['breadcrumbs'][] = ['label'=>'Access Management','url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="access-create">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div><h3 class="mb-1"><i class="bi bi-plus-circle text-primary me-2"></i><?= Html::encode($this->title) ?></h3><div class="small text-muted">Grant page access to a role and department.</div></div>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Back',['index'],['class'=>'btn btn-outline-secondary']) ?>
    </div>
    <?= $this->render('_form',compact('model','roleOptions','departmentOptions','pageOptions')) ?>
</div>
