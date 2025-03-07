<?php
/**
 * Ana Sayfa Controller
 */
class HomeController extends Controller {
    /**
     * Ana sayfa
     */
    public function index() {
        // Tüm model sınıflarını yükle
        require_once MODEL_DIR . '/ModelModel.php';
        require_once MODEL_DIR . '/OrderModel.php';
        require_once MODEL_DIR . '/BoxModel.php';
        require_once MODEL_DIR . '/OrderSizeModel.php';
        
        // Model istatistiklerini al
        $modelModel = new ModelModel();
        $models = $modelModel->getStatistics();
        
        // Sipariş istatistiklerini al
        $orderModel = new OrderModel();
        $orders = $orderModel->getStatistics();
        
        // Koli istatistiklerini al
        $boxModel = new BoxModel();
        $boxes = $boxModel->getStatistics();
        
        // Son eklenen modelleri al
        $recentModels = $modelModel->getRecent(5);
        
        // Görünümü yükle
        $this->render('home/index', [
            'pageTitle' => 'Ana Sayfa',
            'activePage' => 'dashboard',
            'models' => $models,
            'orders' => $orders,
            'boxes' => $boxes,
            'recentModels' => $recentModels
        ]);
    }
}