<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8"/>
    <title><?= $pageTitle ?? APP_NAME ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <meta name="MobileOptimized" content="320">


    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="<?= SITE_URL ?>/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/plugins/fullcalendar/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/plugins/kendo/styles/images/kendoui.woff?v=1.1" as="font" type="font/woff" crossorigin>
    <link href="<?= SITE_URL ?>/assets/plugins/kendo/styles/kendo.common-bootstrap.min.css" rel="stylesheet" rel="preload">
    <link href="<?= SITE_URL ?>/assets/plugins/kendo/styles/kendo.mobile.all.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/plugins/kendo/styles/kendo.dataviz.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/plugins/kendo/styles/kendo.dataviz.bootstrap.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/plugins/kendo/styles/kendo.common-bootstrap.core.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/plugins/kendo/styles/kendo.common.core.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/plugins/kendo/styles/kendo.common.min.css" rel="stylesheet">
    <link href="<?= SITE_URL ?>/assets/plugins/kendo/styles/kendo.bootstrap.mobile.min.css" rel="stylesheet">


    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="<?= SITE_URL ?>/assets/css/style-conquer.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/css/pages/tasks.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="<?= SITE_URL ?>/assets/css/custom.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL ?>/assets/css/site.css" rel="stylesheet" type="text/css"/>

    <!-- END THEME STYLES -->
    

    <!-- Sayfa özel stil dosyaları -->
    <?php if (isset($styles) && is_array($styles)): ?>
        <?php foreach ($styles as $style): ?>
            <link href="<?= asset_url('css/' . $style) ?>" rel="stylesheet" type="text/css"/>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <link rel="shortcut icon" href="<?= asset_url('images/favicon.ico') ?>"/>
</head>
<body class="page-header-fixed">
    <!-- BAŞLIK -->
    <div class="header navbar navbar-fixed-top">
        <div class="header-inner">
            <!-- LOGO -->
            <div class="page-logo">
                <a href="<?= url('/') ?>">
                    <img src="<?= asset_url('images/logo.png') ?>" alt="logo" class="logo-default"/>
                </a>
            </div>
            
            <!-- RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <img src="<?= theme_url('assets/img/menu-toggler.png') ?>" alt=""/>
            </a>
            
            <!-- TOP NAVIGATION MENU -->
            <ul class="nav navbar-nav pull-right">
                <li class="dropdown user">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <img alt="" src="<?= asset_url('images/avatar.png') ?>"/>
                        <span class="username">Kullanıcı</span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= url('/profile') ?>"><i class="fa fa-user"></i> Profil</a>
                        </li>
                        <li>
                            <a href="<?= url('/settings') ?>"><i class="fa fa-cog"></i> Ayarlar</a>
                        </li>
                        <li>
                            <a href="<?= url('/help') ?>"><i class="fa fa-question-circle"></i> Yardım</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= url('/logout') ?>"><i class="fa fa-sign-out"></i> Çıkış</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="clearfix"></div>
    
    <!-- CONTAINER -->
    <div class="page-container">
        <!-- SIDEBAR -->
        <?php include VIEW_DIR . '/layout/sidebar.php'; ?>
        
        <!-- PAGE CONTENT -->
        <div class="page-content-wrapper">
            <div class="page-content">
                <!-- PAGE HEADER -->
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title"><?= $pageTitle ?? APP_NAME ?></h3>
                        <ul class="page-breadcrumb breadcrumb">
                            <li>
                                <i class="fa fa-home"></i>
                                <a href="<?= url('/') ?>">Ana Sayfa</a>
                                <i class="fa fa-angle-right"></i>
                            </li>
                            <?php if (isset($breadcrumbs) && is_array($breadcrumbs)): ?>
                                <?php foreach ($breadcrumbs as $link => $title): ?>
                                    <li>
                                        <a href="<?= url($link) ?>"><?= $title ?></a>
                                        <i class="fa fa-angle-right"></i>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li><?= $pageTitle ?? '' ?></li>
                        </ul>
                    </div>
                </div>
                
                <!-- ALERT MESSAGES -->
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <button class="close" data-close="alert"></button>
                        <?= $_SESSION['success_message'] ?>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <button class="close" data-close="alert"></button>
                        <?= $_SESSION['error_message'] ?>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['info_message'])): ?>
                    <div class="alert alert-info">
                        <button class="close" data-close="alert"></button>
                        <?= $_SESSION['info_message'] ?>
                    </div>
                    <?php unset($_SESSION['info_message']); ?>
                <?php endif; ?>
                
                <!-- PAGE CONTENT -->