<?php
/**
 * Ana Uygulama Sınıfı
 */
class App {
    private $router;
    private $db;
    private static $instance = null;
    
    /**
     * Özel yapıcı metod - Singleton pattern
     */
    private function __construct() {
        // Veritabanı bağlantısı
        $this->db = new Database();
        
        // Router kurulumu
        $this->router = new Router();
        $this->setupRoutes();
    }
    
    /**
     * Singleton instance döndür
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Uygulamayı çalıştır
     */
    public function run() {
        session_start();
        $this->router->dispatch();
    }
    
    /**
     * Veritabanı bağlantısını döndür
     */
    public function getDB() {
        return $this->db;
    }
    
    /**
     * Router'ı yapılandır
     */
    private function setupRoutes() {
        // Ana sayfa
        $this->router->add('/', 'HomeController@index');
        
        // Sipariş işlemleri
        $this->router->add('/orders/import', 'OrderController@importForm');
        $this->router->add('/orders/process-import', 'OrderController@processImport');
        $this->router->add('/orders/list', 'OrderController@listModels');
        $this->router->add('/orders/view/{model_kodu}', 'OrderController@modelDetails');
        $this->router->add('/orders/edit/{id}', 'OrderController@edit');
        $this->router->add('/orders/update', 'OrderController@update');
        $this->router->add('/orders/delete', 'OrderController@delete');
        
        // AJAX işlemleri
        $this->router->add('/ajax/get-model-data', 'AjaxController@getModelData');
        $this->router->add('/ajax/update-cell', 'AjaxController@updateCell');
        $this->router->add('/ajax/delete-row', 'AjaxController@deleteRow');
        
        // Koli işlemleri
        $this->router->add('/boxes/calculate', 'BoxController@calculateForm');
        $this->router->add('/boxes/process-calculation', 'BoxController@processCalculation');
        $this->router->add('/boxes/list', 'BoxController@listBoxes');
        $this->router->add('/boxes/view/{id}', 'BoxController@viewBox');
        $this->router->add('/boxes/update-status', 'BoxController@updateStatus');
        
        // Raporlama
        $this->router->add('/reports', 'ReportController@index');
        $this->router->add('/reports/export', 'ReportController@export');
        
        // Yardım
        $this->router->add('/help', 'HelpController@index');
    }
}