<?php

use yii\helpers\Html;

$types = [
    'success' => [
        'class' => 'success',
        'icon' => 'check-circle-fill',
    ],
    'error' => [
        'class' => 'danger',
        'icon' => 'exclamation-triangle-fill',
    ],
    'warning' => [
        'class' => 'warning',
        'icon' => 'exclamation-circle-fill',
    ],
];

?>

<?php foreach ($types as $type => $config)
{ ?>
    <?php if (Yii::$app->session->hasFlash($type))
    { ?>
        <div class="alert alert-<?= $config['class'] ?> alert-dismissible fade show" role="alert">

            <i class="bi bi-<?= $config['icon'] ?> me-2"></i>

            <?= Html::encode(Yii::$app->session->getFlash($type)) ?>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"></button>

        </div>
    <?php } ?>
<?php } ?>