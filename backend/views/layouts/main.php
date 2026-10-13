<?php

/** @var yii\web\View $this */
/** @var string $content */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap5\Nav; 

// Awtomatikong lo-load mula sa iyong Composer vendor folder nang napakabilis
\yii\bootstrap5\BootstrapIconAsset::register($this); 
\yii\bootstrap5\BootstrapPluginAsset::register($this);
AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    
    <!-- KUMPLETONG STYLING NG DASHBOARD -->
        <style>
        body, html { height: 100%; background-color: #f4f6f9; font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        .wrapper { display: flex; width: 100%; min-height: 100vh; position: relative; }
        
        /* 1. Light Sidebar Layout Base */
        .sidebar { 
            min-width: 250px; max-width: 250px; 
            background-color: #ffffff; min-height: 100vh; 
            border-right: 1px solid #dee2e6;
            transition: min-width 0.15s ease, max-width 0.15s ease;
            position: relative;
        }
        
        /* Mini-icon toggled status (65px kapag nakatago) */
        .sidebar.collapsed { min-width: 65px; max-width: 65px; }
        
        /* Sidebar Header Configuration */
        .sidebar .sidebar-header { 
            padding: 18px; 
            background-color: #f8f9fa; 
            border-bottom: 1px solid #dee2e6;
            font-weight: bold; 
            color: #212529; 
            text-align: center; white-space: nowrap; overflow: hidden; 
        }
        .sidebar.collapsed .sidebar-header span { display: none; }
        .sidebar.collapsed .sidebar-header::after { content: "📦"; font-size: 1.2rem; }

        /* 2. Menu Links Navigation Items UI */
        .sidebar.collapsed .nav-link span { display: none; }
        .sidebar .nav-link { 
            color: #495057; 
            padding: 11px 20px; border-radius: 0; display: flex; align-items: center; white-space: nowrap; 
            font-size: 0.95rem;
        }
        .sidebar .nav-link:hover { 
            background-color: #f1f3f5; 
            color: #212529; 
        }
        .sidebar .nav-link.active { 
            background-color: #e7f1ff !important; 
            color: #0d6efd !important; 
            font-weight: 500;
            border-left: 4px solid #0d6efd; 
        }
        .sidebar .nav-link i { font-size: 1.2rem; min-width: 30px; display: inline-block; }
        
        /* 3. Minimalist at Kulay-less na Pull-In / Pull-Out Tab Handle Bar */
        .sidebar-toggle-container {
            position: absolute; 
            top: 65px; /* Inihanay paitaas malapit sa Dashboard menu link */
            right: -14px; 
            z-index: 1050;
        }
        .sidebar-toggle-bar {
            width: 14px; height: 45px; /* Compact at minimalist tab configuration */
            background-color: #ffffff; border: 1px solid #dee2e6; border-left: none;
            border-radius: 0 5px 5px 0; cursor: pointer; color: #868e96; font-size: 9px;
            box-shadow: 2px 0 4px rgba(0,0,0,0.04); transition: background-color 0.1s, color 0.1s;
        }
        .sidebar-toggle-bar:hover { background-color: #f8f9fa; color: #212529; }
        
        /* Pagpapalit at Pag-ikot ng Handle Icon (< at >) gamit ang Bootstrap Font Code */
        .sidebar .toggle-icon::before { content: "\f282"; } /* bi-chevron-left (<) */
        .sidebar.collapsed .toggle-icon::before { content: "\f285"; } /* bi-chevron-right (>) */
        
        /* 4. Responsive Controls at Transition Hiding */
        .sidebar.collapsed .nav-item-header, .sidebar.collapsed hr { display: none !important; }
        
        /* Main Application Workspace Content Panel */
        .main-content { width: 100%; padding: 25px; min-height: 100vh; background: #ffffff; transition: padding 0.15s; }
        .page-title-block { margin-bottom: 20px; border-bottom: 1px solid #dee2e6; padding-bottom: 10px; }
    </style>

</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<div class="wrapper">
    <!-- SIDEBAR NAVIGATION MENU -->
    <nav id="sidebar" class="sidebar">
        
        <!-- Ang Minimalist Pull Tab Handle -->
        <div class="sidebar-toggle-container">
            <button type="button" id="sidebarCollapse" class="sidebar-toggle-bar d-flex align-items-center justify-content-center p-0">
                <i class="bi toggle-icon m-0"></i>
            </button>
        </div>

        <div class="sidebar-header">
            <span><?= Html::encode(Yii::$app->name) ?></span>
        </div>

        <?php
        echo Nav::widget([
            'options' => ['class' => 'nav nav-pills flex-column w-100'],
            'items' => [
                // --- MAIN ---
                ['label' => '<i class="bi bi-speedometer2"></i> <span>Dashboard</span>', 'url' => ['/site/index']],
                
                // --- TRANSACTIONS ---
                ['label' => '<div class="nav-header small text-uppercase text-muted px-3 pt-3 pb-1 menu-text" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">Transactions</div>', 'encode' => false, 'options' => ['class' => 'nav-item-header']],
                ['label' => '<i class="bi bi-calendar3"></i> <span>Inventory Period</span>', 'url' => ['/inventory-period/index']],
                ['label' => '<i class="bi bi-box-arrow-in-down"></i> <span>Inventory In</span>', 'url' => ['/inventory-in/index']],
                ['label' => '<i class="bi bi-box-arrow-up"></i> <span>Inventory Out</span>', 'url' => ['/inventory-out/index']],
                
                // --- MASTER DATA ---
                ['label' => '<div class="nav-header small text-uppercase text-muted px-3 pt-3 pb-1 menu-text" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">Master Data</div>', 'encode' => false, 'options' => ['class' => 'nav-item-header']],
                ['label' => '<i class="bi bi-box-seam"></i> <span>Products</span>', 'url' => ['/product/index']],
                ['label' => '<i class="bi bi-truck"></i> <span>Suppliers</span>', 'url' => ['/supplier/index']],
                ['label' => '<i class="bi bi-kanban"></i> <span>Projects</span>', 'url' => ['/project/index']],
                ['label' => '<i class="bi bi-rulers"></i> <span>Units</span>', 'url' => ['/unit/index']],
                ['label' => '<i class="bi bi-tags"></i> <span>Product Types</span>', 'url' => ['/product-type/index']],
                
                // --- SYSTEM & SECURITY ---
                ['label' => '<div class="nav-header small text-uppercase text-muted px-3 pt-3 pb-1 menu-text" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">System</div>', 'encode' => false, 'options' => ['class' => 'nav-item-header']],
                ['label' => '<i class="bi bi-building"></i> <span>Departments</span>', 'url' => ['/department/index']],
                ['label' => '<i class="bi bi-shield-lock"></i> <span>Roles</span>', 'url' => ['/role/index']],
                ['label' => '<i class="bi bi-journal-text"></i> <span>Audit Log</span>', 'url' => ['/audit-log/index']],
                ['label' => '<i class="bi bi-file-earmark-text"></i> <span>Pages Management</span>', 'url' => ['/page/index']],
                ['label' => '<i class="bi bi-shield-lock"></i> <span>Access Management</span>', 'url' => ['/access/index']],
				// --- ACCOUNT / EXIT ---
                ['label' => '<hr class="my-2 border-secondary opacity-25 menu-text">', 'encode' => false],
                [
                    'label' => '<i class="bi bi-power"></i> <span>Logout</span>',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post'],
                    'visible' => !Yii::$app->user->isGuest
                ],
            ],
            'encodeLabels' => false,
        ]);
        ?>
    </nav>

    <!-- MAIN WORKSPACE CONTENT PANEL -->
    <div class="main-content">
       
        <div class="container-fluid p-0">
            <!-- Breadcrumbs Navigation -->
            <?php if (isset($this->params['breadcrumbs'])): ?>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-4 small bg-light p-2 rounded-2" style="border: 1px solid #e9ecef;">
                        <?php foreach ($this->params['breadcrumbs'] as $breadcrumb): ?>
                            <?php if (is_array($breadcrumb)): ?>
                                <li class="breadcrumb-item"><a href="<?= \yii\helpers\Url::to($breadcrumb['url']) ?>" class="text-decoration-none"><?= Html::encode($breadcrumb['label']) ?></a></li>
                            <?php else: ?>
                                <li class="breadcrumb-item active" aria-current="page"><?= Html::encode($breadcrumb) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            <?php endif; ?>

            <!-- Dito ipapakita ng Yii2 ang bawat view content (Tables/Forms) -->
            <?= $content ?>
        </div>
    </div>
</div>

<?php 
// Vanilla-speed execution command handler para sa toggle action
$this->registerJs("$('#sidebarCollapse').on('click',function(){\$('#sidebar').toggleClass('collapsed')});");
?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
