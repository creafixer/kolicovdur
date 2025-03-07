<?php
// Veritabanı tablolarını oluşturma sayfası

// Kök dizini tanımla
define('ROOT_DIR', __DIR__);
define('CONFIG_DIR', ROOT_DIR . '/config');
define('CORE_DIR', ROOT_DIR . '/core');

// Temel dosyaları dahil et
require_once CONFIG_DIR . '/config.php';
require_once CORE_DIR . '/Database.php';

// Sayfa başlığı ve stil
echo '<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Koli Çovdur - Kurulum</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 50px; }
        .container { max-width: 800px; }
        .result { margin-top: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>Koli Çovdur - Veritabanı Kurulumu</h3>
            </div>
            <div class="card-body">';

try {
    // Veritabanı bağlantısı oluştur
    $db = new Database();
    
    echo '<div class="alert alert-success">Veritabanı bağlantısı başarılı!</div>';
    
    // Tabloları oluştur
    echo '<h4>Veritabanı tabloları oluşturuluyor...</h4>';
    echo '<ul class="list-group">';
    
    $tables = require CONFIG_DIR . '/database.php';
    
    foreach ($tables as $tableName => $columns) {
        echo '<li class="list-group-item">';
        echo '<strong>' . $tableName . '</strong> tablosu oluşturuluyor... ';
        
        try {
            $db->createTable($tableName, $columns);
            echo '<span class="badge badge-success">Başarılı</span>';
        } catch (Exception $e) {
            echo '<span class="badge badge-danger">Hata</span>';
            echo '<div class="text-danger small">' . $e->getMessage() . '</div>';
        }
        
        echo '</li>';
    }
    
    echo '</ul>';
    
    echo '<div class="alert alert-success mt-4">
            <h4>Kurulum Tamamlandı!</h4>
            <p>Veritabanı tabloları başarıyla oluşturuldu. Şimdi <a href="index.php" class="btn btn-primary btn-sm">Ana Sayfaya</a> gidebilirsiniz.</p>
        </div>';
    
} catch (Exception $e) {
    echo '<div class="alert alert-danger">
            <h4>Hata!</h4>
            <p>' . $e->getMessage() . '</p>
        </div>';
}

echo '    </div>
        </div>
    </div>
</body>
</html>';