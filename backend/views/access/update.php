<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
/** @var backend\models\Accesses $model */
/** @var array $roleOptions */
/** @var array $departmentOptions */
/** @var array $pageOptions */
$this->title = 'Update Access #' . $model->id;
$this->params['breadcrumbs'][] = ['label'=>'Access Management','url'=>['index']];
$this->params['breadcrumbs'][] = ['label'=>'Access #' . $model->id,'url'=>['view','id'=>$model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="access-update">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div><h3 class="mb-1"><i class="bi bi-pencil-square text-primary me-2"></i><?= Html::encode($this->title) ?></h3><div class="small text-muted">Update the selected access rule.</div></div>
        <?= Html::a('<i class="bi bi-arrow-left me-1"></i> Back',['view','id'=>$model->id],['class'=>'btn btn-outline-secondary']) ?>
    </div>
    <?= $this->render('_form',compact('model','roleOptions','departmentOptions','pageOptions')) ?>
</div>
