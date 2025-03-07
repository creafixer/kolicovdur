<?php
/**
 * Temel Controller Sınıfı
 */
class Controller {
    /**
     * Görünüm dosyasını yükle
     * 
     * @param string $view Görünüm dosya adı
     * @param array $data Görünüme aktarılacak veriler
     * @return void
     */
    protected function render($view, $data = []) {
        // Verileri değişkenlere dönüştür
        extract($data);
        
        // Sayfa başlığı belirtilmemişse varsayılanı kullan
        $pageTitle = $pageTitle ?? APP_NAME;
        
        // Aktif sayfa belirtilmemişse varsayılanı kullan
        $activePage = $activePage ?? '';
        
        // Görünüm dosyasını yükle
        include VIEW_DIR . '/layout/header.php';
        include VIEW_DIR . '/' . $view . '.php';
        include VIEW_DIR . '/layout/footer.php';
    }
    
    /**
     * JSON yanıtı döndür
     * 
     * @param array $data JSON verisi
     * @param int $statusCode HTTP durum kodu
     * @return void
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Yönlendirme yap
     * 
     * @param string $url Yönlendirilecek URL
     * @return void
     */
    protected function redirect($url) {
        header('Location: ' . SITE_URL . $url);
        exit;
    }
    
    /**
     * POST verilerini al
     * 
     * @param string $key İstenilen değişken adı
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    protected function post($key, $default = null) {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * GET verilerini al
     * 
     * @param string $key İstenilen değişken adı
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    protected function get($key, $default = null) {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    /**
     * Dosya yükleme işlemlerini kontrol et
     * 
     * @param string $key Form alanı adı
     * @return array|null
     */
    protected function file($key) {
        return isset($_FILES[$key]) ? $_FILES[$key] : null;
    }
}