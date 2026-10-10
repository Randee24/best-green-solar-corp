
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\Json;

/** @var yii\web\View $this */

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

$selectedDate = sprintf('%04d-%02d-01',$year,$month);
$monthLabel = date('F Y',strtotime($selectedDate));

$previousDate = date('Y-m-d',strtotime($selectedDate . ' -1 month'));
$nextDate = date('Y-m-d',strtotime($selectedDate . ' +1 month'));

$previousUrl = Url::to([
    'index',
    'year' => date('Y',strtotime($previousDate)),
    'month' => date('n',strtotime($previousDate)),
]);

$nextUrl = Url::to([
    'index',
    'year' => date('Y',strtotime($nextDate)),
    'month' => date('n',strtotime($nextDate)),
]);

$cards = [
    [
        'title' => 'Inventory IN',
        'value' => number_format($summary['total_in']),
        'icon' => 'bi-box-arrow-in-down',
        'color' => 'success',
        'description' => 'Posted incoming transactions',
    ],
    [
        'title' => 'Inventory OUT',
        'value' => number_format($summary['total_out']),
        'icon' => 'bi-box-arrow-up',
        'color' => 'danger',
        'description' => 'Posted outgoing transactions',
    ],
    [
        'title' => 'Incoming Value',
        'value' => '₱' . number_format($summary['incoming_value'],2),
        'icon' => 'bi-cash-stack',
        'color' => 'primary',
        'description' => 'Total posted inventory purchases',
    ],
    [
        'title' => 'Outgoing Cost',
        'value' => '₱' . number_format($summary['outgoing_cost'],2),
        'icon' => 'bi-receipt',
        'color' => 'warning',
        'description' => 'Total posted FIFO cost',
    ],
];

$chartLabels = array_column($activity,'week');
$chartIncoming = array_column($activity,'incoming');
$chartOutgoing = array_column($activity,'outgoing');
$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js',
    ['position' => \yii\web\View::POS_HEAD]
);
?>

<div class="dashboard-index">

    <?= $this->render('//layouts/_flash') ?>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h3 class="mb-1">
                <i class="bi bi-speedometer2 text-primary me-2"></i>
                Dashboard
            </h3>
            <p class="text-muted mb-0">
                Monthly purchasing and inventory overview
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <?= Html::a(
                '<i class="bi bi-chevron-left"></i>',
                $previousUrl,
                ['class' => 'btn btn-outline-secondary']
            ) ?>

            <span class="fw-semibold px-2">
                <?= Html::encode($monthLabel) ?>
            </span>

            <?= Html::a(
                '<i class="bi bi-chevron-right"></i>',
                $nextUrl,
                ['class' => 'btn btn-outline-secondary']
            ) ?>
        </div>
    </div>

    <div class="row g-3 mb-4">

        <?php foreach ($cards as $card)
        { ?>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="text-muted small">
                                <?= Html::encode($card['title']) ?>
                            </span>

                            <span class="bg-<?= $card['color'] ?> bg-opacity-10 text-<?= $card['color'] ?> rounded-3 p-2">
                                <i class="bi <?= $card['icon'] ?> fs-5"></i>
                            </span>
                        </div>

                        <h3 class="fw-bold mb-1">
                            <?= Html::encode($card['value']) ?>
                        </h3>

                        <small class="text-muted">
                            <?= Html::encode($card['description']) ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>

    <div class="row g-3 mb-4">

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart text-primary me-2"></i>
                        Monthly Inventory Activity
                    </h5>
                </div>

                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="inventoryActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-hourglass-split text-primary me-2"></i>
                        Pending Transactions
                    </h5>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">Inventory IN Drafts</div>
                            <small class="text-muted">Waiting to be posted</small>
                        </div>

                        <?= Html::a(
                            number_format($summary['draft_in']),
                            ['/inventory-in/index'],
                            ['class' => 'badge bg-warning text-dark text-decoration-none fs-6']
                        ) ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3">
                        <div>
                            <div class="fw-semibold">Inventory OUT Drafts</div>
                            <small class="text-muted">Waiting to be posted</small>
                        </div>

                        <?= Html::a(
                            number_format($summary['draft_out']),
                            ['/inventory-out/index'],
                            ['class' => 'badge bg-warning text-dark text-decoration-none fs-6']
                        ) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">
                <i class="bi bi-clock-history text-primary me-2"></i>
                Recent Transactions
            </h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Transaction</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($recentTransactions as $transaction)
                        {
                            $isIncoming = $transaction['transaction_type'] === 'IN';

                            $transactionId = (int) $transaction['id'];

                            $url = $isIncoming
                                ? ['/inventory-in/view','id' => $transactionId]
                                : ['/inventory-out/view','id' => $transactionId];

                            $status = $transaction['status'];
                        ?>
                            <tr>
                                <td class="ps-3 fw-semibold">
                                    <?= Html::encode(
                                        ($isIncoming ? 'IN-' : 'OUT-') .
                                        str_pad($transactionId,5,'0',STR_PAD_LEFT)
                                    ) ?>
                                </td>

                                <td>
                                    <?= Yii::$app->formatter->asDate(
                                        $transaction['transaction_date'],
                                        'php:M d, Y'
                                    ) ?>
                                </td>

                                <td>
                                    <span class="badge bg-<?= $isIncoming ? 'success' : 'danger' ?> bg-opacity-10 text-<?= $isIncoming ? 'success' : 'danger' ?>">
                                        <?= $isIncoming ? 'Inventory IN' : 'Inventory OUT' ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-<?= $status === 'posted' ? 'success' : ($status === 'draft' ? 'warning text-dark' : 'secondary') ?>">
                                        <?= Html::encode(ucfirst($status)) ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <?= Html::a(
                                        '<i class="bi bi-eye"></i>',
                                        $url,
                                        [
                                            'class' => 'btn btn-sm btn-outline-primary',
                                            'title' => 'View Transaction',
                                        ]
                                    ) ?>
                                </td>
                            </tr>
                        <?php } ?>

                        <?php if (empty($recentTransactions))
                        { ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No transactions found for this month.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php

// Chart.js must be loaded in your application or registered separately.
$labels = Json::htmlEncode($chartLabels);
$incoming = Json::htmlEncode($chartIncoming);
$outgoing = Json::htmlEncode($chartOutgoing);

$js = <<<JS
(function()
{
    const canvas = document.getElementById('inventoryActivityChart');

    if (!canvas || typeof Chart === 'undefined')
    {
        return;
    }

    new Chart(canvas,{
        type: 'bar',
        data: {
            labels: {$labels},
            datasets: [
                {
                    label: 'Inventory IN',
                    data: {$incoming},
                    backgroundColor: 'rgba(25,135,84,0.75)',
                    borderRadius: 5
                },
                {
                    label: 'Inventory OUT',
                    data: {$outgoing},
                    backgroundColor: 'rgba(220,53,69,0.75)',
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
})();
JS;

$this->registerJs($js);
?>
