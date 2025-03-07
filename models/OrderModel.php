<?php
/**
 * Sipariş için model sınıfı
 */
class OrderModel extends Model {
    protected $tableName = 'orders';
    
    /**
     * Sipariş istatistiklerini getir
     * 
     * @return array İstatistikler
     */
    public function getStatistics() {
        // Toplam sipariş sayısı
        $this->db->query("SELECT COUNT(*) as total FROM {$this->tableName}");
        $totalOrders = $this->db->fetch()['total'];
        
        // Toplam lot sayısı
        $this->db->query("SELECT SUM(siparis_gecilen_lot_sayisi) as total FROM {$this->tableName}");
        $totalLots = $this->db->fetch()['total'] ?? 0;
        
        // Toplam depo girişi olan lot sayısı
        $this->db->query("SELECT SUM(depo_girisi_olan_lot_sayisi) as total FROM {$this->tableName}");
        $totalDepositedLots = $this->db->fetch()['total'] ?? 0;
        
        // Tamamlanan sipariş sayısı
        $this->db->query("SELECT COUNT(*) as total FROM {$this->tableName} WHERE siparis_gecilen_lot_sayisi = depo_girisi_olan_lot_sayisi");
        $completedOrders = $this->db->fetch()['total'];
        
        // Teslimat ülkelerine göre dağılım
        $this->db->query("SELECT teslimat_ulkesi, COUNT(*) as count FROM {$this->tableName} GROUP BY teslimat_ulkesi ORDER BY count DESC");
        $countriesDistribution = $this->db->fetchAll();
        
        return [
            'total_orders' => $totalOrders,
            'total_lots' => $totalLots,
            'total_deposited_lots' => $totalDepositedLots,
            'completed_orders' => $completedOrders,
            'countries_distribution' => $countriesDistribution
        ];
    }
    
    /**
     * Excel verilerinden sipariş oluştur
     * 
     * @param array $data Excel verileri
     * @return array Oluşturulan siparişlerin ID'leri
     */
    public function createFromExcel($data) {
        $modelModel = new ModelModel();
        $orderSizeModel = new OrderSizeModel();
        
        $orderIds = [];
        
        // İşlemi başlat
        $this->db->beginTransaction();
        
        try {
            // Toplam sipariş tablosu
            foreach ($data['toplam_siparis'] as $orderData) {
                // Model ID'sini al veya oluştur
                $modelId = $modelModel->getOrCreateByModelKodu($orderData['model_kodu']);
                
                // Sipariş verilerini hazırla
                $orderValues = [
                    'model_id' => $modelId,
                    'sezon' => $orderData['sezon'],
                    'siparis_numarasi' => $orderData['siparis_numarasi'],
                    'ship_to' => $orderData['ship_to'],
                    'tedarikci_termini' => $orderData['tedarikci_termini'],
                    'renk_kodu_adi' => $orderData['renk_kodu_adi'],
                    'lot_kodu' => $orderData['lot_kodu'],
                    'set_icerigi' => $orderData['set_icerigi'],
                    'bir_lottaki_urun_sayisi' => $orderData['bir_lottaki_urun_sayisi'],
                    'teslimat_ulkesi' => $orderData['teslimat_ulkesi'],
                    'siparis_gecilen_lot_sayisi' => $orderData['siparis_gecilen_lot_sayisi'],
                    'siparis_gecilen_acik_adet_sayisi' => $orderData['siparis_gecilen_acik_adet_sayisi'],
                    'depo_girisi_olan_lot_sayisi' => $orderData['depo_girisi_olan_lot_sayisi'] ?? 0,
                    'depo_girisi_olan_acik_adet_sayisi' => $orderData['depo_girisi_olan_acik_adet_sayisi'] ?? 0
                ];
                
                // Aynı sipariş var mı kontrol et
                $this->db->query("SELECT id FROM {$this->tableName} 
                                WHERE model_id = :model_id 
                                AND siparis_numarasi = :siparis_numarasi 
                                AND lot_kodu = :lot_kodu");
                $this->db->bind(':model_id', $modelId);
                $this->db->bind(':siparis_numarasi', $orderData['siparis_numarasi']);
                $this->db->bind(':lot_kodu', $orderData['lot_kodu']);
                $existingOrder = $this->db->fetch();
                
                if ($existingOrder) {
                    // Mevcut siparişi güncelle
                    $orderId = $existingOrder['id'];
                    
                    // Önce eski verileri kaydet
                    $this->db->query("SELECT * FROM {$this->tableName} WHERE id = :id");
                    $this->db->bind(':id', $orderId);
                    $oldData = $this->db->fetch();
                    
                    // Siparişi güncelle
                    $this->update($orderId, $orderValues);
                    
                    // Güncelleme kaydı oluş
                    $this->logOrderHistory($orderId, 'update', $oldData, $orderValues);
                } else {
                    // Yeni sipariş oluştur
                    $orderId = $this->create($orderValues);
                    
                    // Oluşturma kaydı oluştur
                    $this->logOrderHistory($orderId, 'create', null, $orderValues);
                }
                
                $orderIds[] = $orderId;
                
                // Beden bilgilerini kaydet
                foreach ($orderData['bedenler'] as $bedenAdi => $adet) {
                    if ($adet > 0) {
                        $orderSizeModel->createOrUpdate($orderId, $bedenAdi, $adet);
                    }
                }
            }
            
            // İşlemi tamamla
            $this->db->commit();
            
            return $orderIds;
        } catch (Exception $e) {
            // Hata durumunda işlemi geri al
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Sipariş geçmişi kaydet
     * 
     * @param int $orderId Sipariş ID
     * @param string $actionType İşlem tipi (create, update, delete)
     * @param array|null $oldData Eski veriler
     * @param array|null $newData Yeni veriler
     * @return int|false Eklenen kaydın ID'si
     */
    private function logOrderHistory($orderId, $actionType, $oldData, $newData) {
        $this->db->query("INSERT INTO order_history (order_id, action_type, old_data, new_data, user) 
                        VALUES (:order_id, :action_type, :old_data, :new_data, :user)");
        $this->db->bind(':order_id', $orderId);
        $this->db->bind(':action_type', $actionType);
        $this->db->bind(':old_data', $oldData ? json_encode($oldData) : null);
        $this->db->bind(':new_data', $newData ? json_encode($newData) : null);
        $this->db->bind(':user', $_SESSION['user_name'] ?? 'system');
        
        $this->db->execute();
        return $this->db->lastInsertId();
    }
    
    /**
     * Model koduna göre siparişleri getir
     * 
     * @param string $modelKodu Model kodu
     * @param string|null $siparisNumarasi Sipariş numarası
     * @return array Sipariş listesi
     */
    public function getOrdersByModel($modelKodu, $siparisNumarasi = null) {
        $modelModel = new ModelModel();
        $model = $modelModel->getByModelKodu($modelKodu);
        
        if (!$model) {
            return [];
        }
        
        $sql = "SELECT o.*, m.model_kodu 
                FROM {$this->tableName} o 
                JOIN models m ON o.model_id = m.id 
                WHERE o.model_id = :model_id";
        
        $params = [
            'model_id' => $model['id']
        ];
        
        if ($siparisNumarasi) {
            $sql .= " AND o.siparis_numarasi = :siparis_numarasi";
            $params['siparis_numarasi'] = $siparisNumarasi;
        }
        
        $sql .= " ORDER BY o.siparis_numarasi, o.lot_kodu";
        
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        $orders = $this->db->fetchAll();
        
        // Beden bilgilerini ekle
        $orderSizeModel = new OrderSizeModel();
        foreach ($orders as &$order) {
            $order['bedenler'] = $orderSizeModel->getByOrderId($order['id']);
        }
        
        return $orders;
    }
    
    /**
     * Sipariş detaylarını getir
     * 
     * @param int $orderId Sipariş ID
     * @return array|false Sipariş detayları
     */
    public function getOrderDetails($orderId) {
        $this->db->query("SELECT o.*, m.model_kodu 
                        FROM {$this->tableName} o 
                        JOIN models m ON o.model_id = m.id 
                        WHERE o.id = :id");
        $this->db->bind(':id', $orderId);
        $order = $this->db->fetch();
        
        if (!$order) {
            return false;
        }
        
        // Beden bilgilerini ekle
        $orderSizeModel = new OrderSizeModel();
        $order['bedenler'] = $orderSizeModel->getByOrderId($orderId);
        
        // Koli bilgilerini ekle
        $boxModel = new BoxModel();
        $order['boxes'] = $boxModel->getBoxesByOrderId($orderId);
        
        // Sipariş geçmişini ekle
        $this->db->query("SELECT * FROM order_history WHERE order_id = :order_id ORDER BY created_at DESC");
        $this->db->bind(':order_id', $orderId);
        $order['history'] = $this->db->fetchAll();
        
        return $order;
    }
}