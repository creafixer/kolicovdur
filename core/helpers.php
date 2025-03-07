<?php
/**
 * Yardımcı fonksiyonlar
 */

/**
 * URL oluştur
 * 
 * @param string $path URL yolu
 * @return string Tam URL
 */
function url($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}

/**
 * Tema URL'i oluştur
 * 
 * @param string $path Tema dosya yolu
 * @return string Tam tema URL'i
 */
function theme_url($path = '') {
    return THEME_URL . '/' . ltrim($path, '/');
}

/**
 * Asset URL'i oluştur
 * 
 * @param string $path Asset dosya yolu
 * @return string Tam asset URL'i
 */
function asset_url($path = '') {
    return SITE_URL . '/assets/' . ltrim($path, '/');
}

/**
 * Tarihi formatla
 * 
 * @param string $date Tarih
 * @param string $format Tarih formatı
 * @return string Formatlanmış tarih
 */
function format_date($date, $format = DATE_FORMAT) {
    if (!$date) return '';
    $dateObj = new DateTime($date);
    return $dateObj->format($format);
}

/**
 * Para birimini formatla
 * 
 * @param float $amount Miktar
 * @param string $currency Para birimi
 * @return string Formatlanmış para birimi
 */
function format_currency($amount, $currency = 'TL') {
    return number_format($amount, 2, ',', '.') . ' ' . $currency;
}

/**
 * Güvenli metin çıktısı
 * 
 * @param string $text Metin
 * @return string Güvenli metin
 */
function e($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Debug çıktısı
 * 
 * @param mixed $data Debug edilecek veri
 * @param bool $die Çıktıdan sonra sonlandır
 * @return void
 */
function debug($data, $die = true) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    if ($die) die();
}

/**
 * Şu anki URL'i kontrol et
 * 
 * @param string $path Kontrol edilecek URL yolu
 * @return bool URL eşleşme durumu
 */
function is_current_url($path) {
    $currentUrl = isset($_GET['url']) ? $_GET['url'] : '';
    $currentUrl = trim($currentUrl, '/');
    $path = trim($path, '/');
    
    return $currentUrl === $path;
}

/**
 * Aktif sınıfını döndür
 * 
 * @param string $path Kontrol edilecek URL yolu
 * @param string $className Eklenecek sınıf adı
 * @return string Sınıf adı veya boş string
 */
function active_class($path, $className = 'active') {
    return is_current_url($path) ? $className : '';
}

/**
 * Rastgele string oluştur
 * 
 * @param int $length Uzunluk
 * @return string Rastgele string
 */
function random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Dosya boyutunu formatla
 * 
 * @param int $bytes Bayt cinsinden boyut
 * @return string Formatlanmış boyut
 */
function format_file_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Slug oluştur
 * 
 * @param string $text Metin
 * @return string Slug
 */
function slugify($text) {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // Trim
    $text = trim($text, '-');

    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // Lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}