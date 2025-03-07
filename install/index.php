<?php
/**
 * Koli Çovdur - Kurulum Dosyası
 * 
 * Bu dosya, uygulamanın veritabanı tablolarını oluşturur ve ilk ayarları yapar.
 */

// Kök dizini tanımla
define('ROOT_DIR', dirname(__DIR__));
define('CONFIG_DIR', ROOT_DIR . '/config');
define('CORE_DIR', ROOT_DIR . '/core');

// Konfigürasyon dosyasını dahil et
require_once CONFIG_DIR . '/config.php';

// Veritabanı bağlantısı
try {
    $dsn = 'mysql:host=' . DB_HOST . ';charset=' . DB_CHARSET;
    $options = [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    
    $db = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // Veritabanını oluştur
    $db->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Veritabanını seç
    $db->exec("USE `" . DB_NAME . "`");
    
    // SQL dosyasını oku
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // SQL sorgularını çalıştır
    $db->exec($sql);
    
    echo '<div style="background-color: #dff0d8; color: #3c763d; padding: 15px; margin: 20px; border-radius: 4px;">';
    echo '<h3>Kurulum Başarılı!</h3>';
    echo '<p>Veritabanı tabloları başarıyla oluşturuldu.</p>';
    echo '<p><a href="' . SITE_URL . '">Ana Sayfaya Git</a></p>';
    echo '</div>';
} catch (PDOException $e) {
    echo '<div style="background-color: #f2dede; color: #a94442; padding: 15px; margin: 20px; border-radius: 4px;">';
    echo '<h3>Kurulum Hatası!</h3>';
    echo '<p>Veritabanı tabloları oluşturulurken bir hata oluştu:</p>';
    echo '<pre>' . $e->getMessage() . '</pre>';
    echo '</div>';
}