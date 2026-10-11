<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\InventoryIns $model */

$this->title = 'Create Inventory Ins';
$this->params['breadcrumbs'][] = ['label' => 'Inventory Ins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="inventory-ins-create">

	
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h3 class="fw-bold mb-1">
				<i class="bi bi-box-arrow-in-down text-primary me-2"></i>
				Create Inventory IN
			</h3>

			<p class="text-muted mb-0">
				Record incoming inventory products from a supplier.
			</p>
		</div>

		<?= Html::a(
			'<i class="bi bi-arrow-left me-1"></i> Back',
			['index'],
			['class' => 'btn btn-outline-secondary']
		) ?>
	</div>

	<!-- FLASH MESSAGES -->
	<?php foreach (['success','error','warning'] as $type)
	{
		if (Yii::$app->session->hasFlash($type))
		{
			$alertClass = $type === 'error' ? 'danger' : $type;
			?>

			<div class="alert alert-<?= $alertClass ?> alert-dismissible fade show" role="alert">
				<?= Html::encode(Yii::$app->session->getFlash($type)) ?>

				<button type="button"
						class="btn-close"
						data-bs-dismiss="alert"
						aria-label="Close">
				</button>
			</div>

			<?php
		}
	} ?>

	<!-- VALIDATION ERRORS -->
	<?php if ($model->hasErrors())
	{ ?>
		<div class="alert alert-danger">
			<div class="fw-semibold mb-2">
				<i class="bi bi-exclamation-triangle me-1"></i>
				Please correct the following errors:
			</div>

			<?= Html::errorSummary($model,[
				'header' => '',
				'class' => 'mb-0',
			]) ?>
		</div>
	<?php } ?>

	<?= $this->render('_form',[
		'model' => $model,
		'products' => $products,
		'suppliers' => $suppliers,
		'items' => $items,
	]) ?>

</div>
