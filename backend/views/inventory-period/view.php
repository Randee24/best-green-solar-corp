
<?php
use yii\bootstrap5\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = date(
    'F Y',
    mktime(0,0,0,$model->month,1,$model->year)
);

$this->params['breadcrumbs'][] = [
    'label' => 'Inventory Periods',
    'url' => ['index'],
];

$this->params['breadcrumbs'][] = $this->title;

$currentYear = (int) date('Y');
$currentMonth = (int) date('n');

$isPrevious = (int) $model->year < $currentYear
    || (
        (int) $model->year === $currentYear
        && (int) $model->month < $currentMonth
    );

$canClose = $model->status === 'open' && $isPrevious;

$inventoryInCount = $model->getInventoryIns()->count();
$inventoryOutCount = $model->getInventoryOuts()->count();

$draftInCount = $model->getInventoryIns()
    ->andWhere(['status' => 'draft'])
    ->count();

$draftOutCount = $model->getInventoryOuts()
    ->andWhere(['status' => 'draft'])
    ->count();

?>

<div class="inventory-period-view">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-calendar-check text-primary me-2"></i>
                <?= Html::encode($this->title) ?>
            </h3>

            <span class="badge bg-<?= $model->status === 'open' ? 'success' : 'secondary' ?>">
                <?= Html::encode(ucfirst($model->status)) ?>
            </span>
        </div>

        <div class="d-flex gap-2">
            <?= Html::a(
                '<i class="bi bi-arrow-left me-1"></i> Back',
                ['index'],
                ['class' => 'btn btn-outline-secondary']
            ) ?>

            <?php if ($canClose)
			{ ?>
				<?= Html::button(
					'<i class="bi bi-lock me-1"></i> Close Period',
					[
						'class' => 'btn btn-danger',
						'type' => 'button',
						'data-bs-toggle' => 'modal',
						'data-bs-target' => '#closePeriodModal',
					]
				) ?>
			<?php } ?>
        </div>
    </div>

    <?php if ($model->status === 'open' && !$isPrevious)
    { ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            This inventory period cannot be closed until a later month.
        </div>
    <?php } ?>

    <?php if ($canClose && ($draftInCount > 0 || $draftOutCount > 0))
    { ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            This period has
            <?= number_format($draftInCount) ?> Inventory IN draft(s)
            and <?= number_format($draftOutCount) ?> Inventory OUT draft(s).
            Resolve these transactions before closing.
        </div>
    <?php } ?>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Inventory IN</p>
                            <h2 class="fw-bold mb-1">
                                <?= number_format($inventoryInCount) ?>
                            </h2>
                            <small class="text-muted">Incoming transactions</small>
                        </div>
                        <i class="bi bi-box-arrow-in-down text-success fs-2"></i>
                    </div>

                    <div class="mt-3">
                        <?= Html::a(
                            'View Inventory IN <i class="bi bi-arrow-right"></i>',
                            ['/inventory-in/index'],
                            ['class' => 'text-decoration-none']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-2">Inventory OUT</p>
                            <h2 class="fw-bold mb-1">
                                <?= number_format($inventoryOutCount) ?>
                            </h2>
                            <small class="text-muted">Outgoing transactions</small>
                        </div>
                        <i class="bi bi-box-arrow-up text-danger fs-2"></i>
                    </div>

                    <div class="mt-3">
                        <?= Html::a(
                            'View Inventory OUT <i class="bi bi-arrow-right"></i>',
                            ['/inventory-out/index'],
                            ['class' => 'text-decoration-none']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-info-circle text-primary me-2"></i>
                Period Information
            </h5>
        </div>

        <div class="card-body p-0">
            <?= DetailView::widget([
                'model' => $model,
                'options' => [
                    'class' => 'table table-striped align-middle mb-0',
                ],
                'attributes' => [
                    'id',
                    [
                        'label' => 'Inventory Month',
                        'value' => $this->title,
                    ],
                    'year',
                    [
                        'attribute' => 'status',
                        'value' => ucfirst($model->status),
                    ],
                    [
                        'attribute' => 'closed_at',
                        'format' => ['datetime','php:M d, Y h:i A'],
                        'value' => $model->closed_at ?: null,
                    ],
                    [
                        'attribute' => 'closed_by',
                        'label' => 'Closed By (User ID)',
                        'value' => $model->closed_by ?: '—',
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>

<?php if ($canClose)
{ ?>
    <div class="modal fade" id="closePeriodModal" tabindex="-1" aria-labelledby="closePeriodTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <?= Html::beginForm(
                    ['close','id' => $model->id],
                    'post'
                ) ?>

                <div class="modal-header">
                    <h5 class="modal-title" id="closePeriodTitle">
                        <i class="bi bi-lock text-danger me-2"></i>
                        Confirm Period Closure
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p>
                        You are about to close the inventory period for
                        <strong><?= Html::encode($this->title) ?></strong>.
                    </p>

                    <div class="alert alert-warning small">
                        Once closed, this period cannot be reopened.
                        Make sure all transactions have been finalized.
                    </div>

                    <label for="pin_code" class="form-label fw-semibold">
                        Enter your PIN code to confirm
                    </label>

                    <?= Html::passwordInput('pin_code','',[
                        'id' => 'pin_code',
                        'class' => 'form-control',
                        'placeholder' => 'Enter PIN code',
                        'inputmode' => 'numeric',
                        'autocomplete' => 'off',
                        'required' => true,
                        'minlength' => 4,
                        'maxlength' => 8,
                    ]) ?>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <?= Html::submitButton(
                        '<i class="bi bi-lock-fill me-1"></i> Confirm & Close Period',
                        ['class' => 'btn btn-danger']
                    ) ?>
                </div>

                <?= Html::endForm() ?>

            </div>
        </div>
    </div>
<?php } ?>

<?php
if ($canClose && Yii::$app->request->get('close'))
{
    $this->registerJs(
        "bootstrap.Modal.getOrCreateInstance(document.getElementById('closePeriodModal')).show();"
    );
}
?>
