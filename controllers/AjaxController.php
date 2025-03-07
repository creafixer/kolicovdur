<?php
/**
 * AJAX Controller
 */
class AjaxController extends Controller {
    /**
     * Model verilerini getir
     */
    public function getModelData() {
        $modelKodu = $this->post('model_kodu');
        
        if (!$modelKodu) {
            $this->json([
                'success' => false,
                'error' => 'Model kodu gereklidir'
            ]);
        }
        
        $modelModel = new ModelModel();
        $model = $modelModel->getByModelKodu($modelKodu);
        
        if (!$model) {
            $this->json([
                'success' => false,
                'error' => 'Model bulunamadı'
            ]);
        }
        
        $orderModel = new OrderModel();
        $orders = $orderModel->getOrdersByModel($modelKodu);
        
        $this->json([
            'success' => true,
            'model' => $model,
            'orders' => $orders
        ]);
    }
    
    /**
     * Hücre değerini güncelle
     */
    public function updateCell() {
        $rowId = $this->post('row_id');
        $field = $this->post('field');
        $value = $this->post('value');
        
        // Alan adının güvenliği
        $allowedFields = [
            'depo_girisi_olan_lot_sayisi',
            'depo_girisi_olan_acik_adet_sayisi'
        ];
        
        if (!in_array($field, $allowedFields)) {
            $this->json([
                'success' => false,
                'error' => 'Geçersiz alan adı'
            ]);
        }
        
        $orderModel = new OrderModel();
        $order = $orderModel->getById($rowId);
        
        if (!$order) {
            $this->json([
                'success' => false,
                'error' => 'Sipariş bulunamadı'
            ]);
        }
        
        // Değeri güncelle
        $result = $orderModel->update($rowId, [
            $field => $value
        ]);
        
        if ($result) {
            $this->json([
                'success' => true,
                'message' => 'Değer başarıyla güncellendi'
            ]);
        } else {
            $this->json([
                'success' => false,
                'error' => 'Değer güncellenirken hata oluştu'
            ]);
        }
    }
    
    /**
     * Satır sil
     */
    public function deleteRow() {
        $rowId = $this->post('row_id');
        $table = $this->post('table');
        
        // Tablo adının güvenliği
        $allowedTables = [
            'orders',
            'boxes',
            'box_labels'
        ];
        
        if (!in_array($table, $allowedTables)) {
            $this->json([
                'success' => false,
                'error' => 'Geçersiz tablo adı'
            ]);
        }
        
        // Tabloya göre model sınıfını belirle
        switch ($table) {
            case 'orders':
                $model = new OrderModel();
                break;
            case 'boxes':
                $model = new BoxModel();
                break;
            case 'box_labels':
                $model = new BoxLabelModel();
                break;
            default:
                $model = null;
        }
        
        if (!$model) {
            $this->json([
                'success' => false,
                'error' => 'Geçersiz model'
            ]);
        }
        
        // Satırı sil
        $result = $model->delete($rowId);
        
        if ($result) {
            $this->json([
                'success' => true,
                'message' => 'Satır başarıyla silindi'
            ]);
        } else {
            $this->json([
                'success' => false,
                'error' => 'Satır silinirken hata oluştu'
            ]);
        }
    }
    
    /**
     * Model arama
     */
    public function searchModel() {
        $term = $this->get('term');
        
        if (!$term) {
            $this->json([]);
        }
        
        $modelModel = new ModelModel();
        
        $this->db = App::getInstance()->getDB();
        $this->db->query("SELECT model_kodu FROM models WHERE model_kodu LIKE :term ORDER BY model_kodu LIMIT 10");
        $this->db->bind(':term', '%' . $term . '%');
        $models = $this->db->fetchAll();
        
        $results = [];
        foreach ($models as $model) {
            $results[] = [
                'id' => $model['model_kodu'],
                'text' => $model['model_kodu']
            ];
        }
        
        $this->json($results);
    }
    
    /**
     * Sipariş arama
     */
    public function searchOrder() {
        $term = $this->get('term');
        $modelKodu = $this->get('model_kodu');
        
        if (!$term) {
            $this->json([]);
        }
        
        $this->db = App::getInstance()->getDB();
        
        $sql = "SELECT DISTINCT o.siparis_numarasi 
                FROM orders o 
                JOIN models m ON o.model_id = m.id 
                WHERE o.siparis_numarasi LIKE :term";
        
        $params = [
            'term' => '%' . $term . '%'
        ];
        
        if ($modelKodu) {
            $sql .= " AND m.model_kodu = :model_kodu";
            $params['model_kodu'] = $modelKodu;
        }
        
        $sql .= " ORDER BY o.siparis_numarasi LIMIT 10";
        
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        $orders = $this->db->fetchAll();
        
        $results = [];
        foreach ($orders as $order) {
            $results[] = [
                'id' => $order['siparis_numarasi'],
                'text' => $order['siparis_numarasi']
            ];
        }
        
        $this->json($results);
    }
}