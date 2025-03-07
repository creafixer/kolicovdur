<?php
// Hata raporlamayı etkinleştir
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kök dizini tanımla
define('ROOT_DIR', __DIR__);
define('CONFIG_DIR', ROOT_DIR . '/config');
define('CORE_DIR', ROOT_DIR . '/core');
define('MODEL_DIR', ROOT_DIR . '/models');
define('VIEW_DIR', ROOT_DIR . '/views');
define('CONTROLLER_DIR', ROOT_DIR . '/controllers');
define('ASSET_DIR', ROOT_DIR . '/assets');

// Temel dosyaları dahil et
require_once CONFIG_DIR . '/config.php';
require_once CORE_DIR . '/App.php';
require_once CORE_DIR . '/Database.php';
require_once CORE_DIR . '/Router.php';
require_once CORE_DIR . '/Controller.php';
require_once CORE_DIR . '/Model.php';
require_once CORE_DIR . '/helpers.php';
require_once CORE_DIR . '/autoload.php';

// Uygulamayı başlat
$app = App::getInstance();
$app->run();