
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\Modal;
use yii\grid\GridView;

$this->title = 'Inventory Periods';
$this->params['breadcrumbs'][] = $this->title;

$currentYear = (int) date('Y');
$currentMonth = (int) date('n');

?>

<div class="inventory-period-index">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-calendar3 text-primary me-2"></i>
                Inventory Periods
            </h3>
            <p class="text-muted mb-0">
                Monthly inventory records are created automatically.
            </p>
        </div>

        <span class="badge bg-light text-dark border px-3 py-2">
            <i class="bi bi-calendar-check me-1"></i>
            <?= Html::encode(date('F Y')) ?>
        </span>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-list-check text-primary me-2"></i>
                Monthly Inventory Records
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
				
				<?php

				$filterValues = Yii::$app->request->get($searchModel->formName(),[]);

				$hasFilters = !empty(array_filter($filterValues,function ($value)
				{
					return $value !== '' && $value !== null;
				}));

				?>

				<div class="card border shadow-sm mb-3 overflow-hidden">
					<div class="card-header bg-light p-0" id="inventoryPeriodSearchHeading">
						<button class="btn w-100 text-start border-0 rounded-0 px-3 py-3 d-flex align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#inventoryPeriodSearchCollapse" aria-expanded="<?= $hasFilters ? 'true' : 'false' ?>" aria-controls="inventoryPeriodSearchCollapse">
							<span class="fw-semibold">
								<i class="bi bi-funnel me-2 text-primary"></i>
								Search Filter

								<?php if ($hasFilters): ?>
									<span class="badge bg-primary ms-2">Filters Applied</span>
								<?php endif; ?>
							</span>

							<i class="bi bi-chevron-down text-secondary"></i>
						</button>
					</div>

					<div class="collapse <?= $hasFilters ? 'show' : '' ?>" id="inventoryPeriodSearchCollapse">
						<div class="card-body border-top">
							<?= $this->render('_search',[
								'model' => $searchModel,
							]) ?>
						</div>
					</div>
				</div>

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'tableOptions' => [
                        'class' => 'table table-hover align-middle mb-0',
                    ],
                    'summaryOptions' => [
                        'class' => 'text-muted small px-3 py-2',
                    ],
					'columns' => [
						[
							'attribute' => 'id',
							'headerOptions' => [
								'style' => 'width: 80px;',
							],
						],
						[
							'attribute' => 'month',
							'label' => 'Month',
							'format' => 'raw',
							'filter' => [
								1 => 'January',
								2 => 'February',
								3 => 'March',
								4 => 'April',
								5 => 'May',
								6 => 'June',
								7 => 'July',
								8 => 'August',
								9 => 'September',
								10 => 'October',
								11 => 'November',
								12 => 'December',
							],
							'filterInputOptions' => [
								'class' => 'form-select form-select-sm',
								'prompt' => 'All Months',
							],
							'value' => function($model)
							{
								$monthName = date(
									'F',
									mktime(0,0,0,$model->month,1,$model->year)
								);

								return Html::a(
									Html::encode($monthName),
									['view','id' => $model->id],
									['class' => 'fw-semibold text-decoration-none']
								);
							},
						],
						[
							'attribute' => 'year',
							'filterInputOptions' => [
								'class' => 'form-control form-control-sm',
								'placeholder' => 'Year',
							],
							'headerOptions' => [
								'style' => 'width: 130px;',
							],
						],
						[
							'attribute' => 'status',
							'format' => 'raw',
							'filter' => [
								'open' => 'Open',
								'closed' => 'Closed',
							],
							'filterInputOptions' => [
								'class' => 'form-select form-select-sm',
								'prompt' => 'All Statuses',
							],
							'value' => function($model)
							{
								return Html::tag(
									'span',
									ucfirst($model->status),
									[
										'class' => 'badge bg-' .
											($model->status === 'open' ? 'success' : 'secondary'),
									]
								);
							},
							'headerOptions' => [
								'style' => 'width: 150px;',
							],
						],
						[
							'label' => 'Period',
							'format' => 'raw',
							'value' => function($model) use ($currentYear,$currentMonth)
							{
								$isCurrent = (int) $model->year === $currentYear
									&& (int) $model->month === $currentMonth;

								if ($isCurrent)
								{
									return '<span class="text-primary small fw-semibold">Current Month</span>';
								}

								return '<span class="text-muted small">Previous / Other</span>';
							},
							'filter' => false,
						],
						[
							'class' => 'yii\grid\ActionColumn',
							'header' => 'Actions',
							'template' => '{view} {close}',
							'headerOptions' => [
								'class' => 'text-center',
								'style' => 'width: 150px;',
							],
							'contentOptions' => [
								'class' => 'text-center',
							],
							'buttons' => [
								'view' => function($url,$model)
								{
									return Html::a(
										'<i class="bi bi-eye"></i>',
										['view','id' => $model->id],
										[
											'class' => 'btn btn-sm btn-outline-primary me-1',
											'title' => 'View',
										]
									);
								},
								
								'close' => function($url,$model) use ($currentYear,$currentMonth)
								{
									$isPrevious = (int) $model->year < $currentYear
										|| (
											(int) $model->year === $currentYear
											&& (int) $model->month < $currentMonth
										);

									if ($model->status !== 'open' || !$isPrevious)
									{
										return '';
									}

									return Html::button(
										'<i class="bi bi-lock"></i>',
										[
											'class' => 'btn btn-sm btn-outline-danger',
											'title' => 'Close Period',
											'type' => 'button',
											'data-bs-toggle' => 'modal',
											'data-bs-target' => '#closePeriodModal',
											'data-period-id' => $model->id,
											'data-period-name' => date(
												'F Y',
												mktime(0,0,0,$model->month,1,$model->year)
											),
										]
									);
								},

							],
						],
					],

                ]) ?>
				
				<?php
				Modal::begin([
					'id' => 'closePeriodModal',
					'title' => '<i class="bi bi-lock text-danger me-2"></i> Confirm Period Closure',
					'size' => Modal::SIZE_DEFAULT,
				]);

				?>

				<?= Html::beginForm(
					['close'],
					'post',
					['id' => 'closePeriodForm']
				) ?>

				<p>
					You are about to close the inventory period for
					<strong id="closePeriodName"></strong>.
				</p>

				<div class="alert alert-warning">
					<i class="bi bi-exclamation-triangle me-2"></i>
					This action cannot be undone.
				</div>

				<div class="mb-3">
					<label for="closePeriodPin" class="form-label">
						Enter your PIN code
					</label>

					<?= Html::passwordInput('pin_code','',[
						'id' => 'closePeriodPin',
						'class' => 'form-control',
						'placeholder' => 'Enter PIN code',
						'autocomplete' => 'off',
						'inputmode' => 'numeric',
						'required' => true,
						'minlength' => 4,
						'maxlength' => 8,
					]) ?>
				</div>

				<div class="text-end">
					<?= Html::button('Cancel',[
						'class' => 'btn btn-outline-secondary',
						'data-bs-dismiss' => 'modal',
					]) ?>

					<?= Html::submitButton(
						'<i class="bi bi-lock-fill me-1"></i> Confirm & Close',
						['class' => 'btn btn-danger ms-2']
					) ?>
				</div>

				<?= Html::endForm() ?>

				<?php Modal::end(); ?>

            </div>
        </div>
    </div>

</div>


<?php

$closeUrl = Url::to(['close']);

$js = <<<JS
const closePeriodModal = document.getElementById('closePeriodModal');

if (closePeriodModal)
{
    closePeriodModal.addEventListener('show.bs.modal',function(event)
    {
        const button = event.relatedTarget;

        if (!button)
        {
            return;
        }

        const periodId = button.getAttribute('data-period-id');
        const periodName = button.getAttribute('data-period-name');

        const form = document.getElementById('closePeriodForm');
        const nameElement = document.getElementById('closePeriodName');
        const pinInput = document.getElementById('closePeriodPin');

        form.action = {$this->renderDynamic('return json_encode(' . var_export($closeUrl,true) . ');')};

        const url = new URL(form.action,window.location.href);
        url.searchParams.set('id',periodId);

        form.action = url.toString();
        nameElement.textContent = periodName;
        pinInput.value = '';
    });
}
JS;

$this->registerJs($js);

?>

