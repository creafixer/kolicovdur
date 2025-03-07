<?php
/**
 * Raporlama Model
 */
class ReportModel extends Model {
    /**
     * Genel rapor
     * 
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    public function getGeneralReport($filters) {
        // Model istatistikleri
        $modelModel = new ModelModel();
        $modelStats = $modelModel->getStatistics();
        
        // Sipariş istatistikleri
        $orderModel = new OrderModel();
        $orderStats = $orderModel->getStatistics();
        
        // Koli istatistikleri
        $boxModel = new BoxModel();
        $boxStats = $boxModel->getStatistics();
        
        // Son eklenen modeller
        $recentModels = $modelModel->getRecent(10);
        
        // Tamamlanan siparişler
        $completedOrders = $this->getCompletedOrders($filters);
        
        // Bekleyen siparişler
        $pendingOrders = $this->getPendingOrders($filters);
        
        return [
            'model_stats' => $modelStats,
            'order_stats' => $orderStats,
            'box_stats' => $boxStats,
            'recent_models' => $recentModels,
            'completed_orders' => $completedOrders,
            'pending_orders' => $pendingOrders
        ];
    }
    
    /**
     * Model raporu
     * 
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    public function getModelReport($filters) {
        $sql = "SELECT m.*, 
                (SELECT COUNT(*) FROM orders o WHERE o.model_id = m.id) as order_count,
                (SELECT SUM(siparis_gecilen_lot_sayisi) FROM orders o WHERE o.model_id = m.id) as total_lots,
                (SELECT SUM(depo_girisi_olan_lot_sayisi) FROM orders o WHERE o.model_id = m.id) as delivered_lots,
                (SELECT COUNT(*) FROM boxes b WHERE b.model_id = m.id) as box_count
                FROM models m
                WHERE 1=1";
        
        $params = [];
        
        // Filtreler
        if (!empty($filters['model_kodu'])) {
            $sql .= " AND m.model_kodu LIKE :model_kodu";
            $params['model_kodu'] = '%' . $filters['model_kodu'] . '%';
        }
        
        if (!empty($filters['tarih_baslangic'])) {
            $sql .= " AND m.created_at >= :tarih_baslangic";
            $params['tarih_baslangic'] = $filters['tarih_baslangic'] . ' 00:00:00';
        }
        
        if (!empty($filters['tarih_bitis'])) {
            $sql .= " AND m.created_at <= :tarih_bitis";
            $params['tarih_bitis'] = $filters['tarih_bitis'] . ' 23:59:59';
        }
        
        $sql .= " ORDER BY m.model_kodu ASC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Koli raporu
     * 
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    public function getBoxReport($filters) {
        $sql = "SELECT b.*, 
                m.model_kodu, 
                o.siparis_numarasi, o.teslimat_ulkesi, o.lot_kodu,
                (SELECT label_status FROM box_labels bl WHERE bl.box_id = b.id ORDER BY bl.created_at DESC LIMIT 1) as label_status
                FROM boxes b
                JOIN models m ON b.model_id = m.id
                JOIN orders o ON b.siparis_id = o.id
                WHERE 1=1";
        
        $params = [];
        
        // Filtreler
        if (!empty($filters['model_kodu'])) {
            $sql .= " AND m.model_kodu LIKE :model_kodu";
            $params['model_kodu'] = '%' . $filters['model_kodu'] . '%';
        }
        
        if (!empty($filters['siparis_numarasi'])) {
            $sql .= " AND o.siparis_numarasi LIKE :siparis_numarasi";
            $params['siparis_numarasi'] = '%' . $filters['siparis_numarasi'] . '%';
        }
        
        if (!empty($filters['teslimat_ulkesi'])) {
            $sql .= " AND o.teslimat_ulkesi = :teslimat_ulkesi";
            $params['teslimat_ulkesi'] = $filters['teslimat_ulkesi'];
        }
        
        if (!empty($filters['durum'])) {
            $sql .= " AND b.status = :durum";
            $params['durum'] = $filters['durum'];
        }
        
        if (!empty($filters['tarih_baslangic'])) {
            $sql .= " AND b.created_at >= :tarih_baslangic";
            $params['tarih_baslangic'] = $filters['tarih_baslangic'] . ' 00:00:00';
        }
        
        if (!empty($filters['tarih_bitis'])) {
            $sql .= " AND b.created_at <= :tarih_bitis";
            $params['tarih_bitis'] = $filters['tarih_bitis'] . ' 23:59:59';
        }
        
        $sql .= " ORDER BY m.model_kodu ASC, o.siparis_numarasi ASC, b.box_number ASC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Etiket raporu
     * 
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    public function getLabelReport($filters) {
        $sql = "SELECT bl.*, 
                b.box_type, b.lot_count, b.box_number, b.status as box_status,
                m.model_kodu, 
                o.siparis_numarasi, o.teslimat_ulkesi, o.lot_kodu
                FROM box_labels bl
                JOIN boxes b ON bl.box_id = b.id
                JOIN models m ON b.model_id = m.id
                JOIN orders o ON b.siparis_id = o.id
                WHERE 1=1";
        
        $params = [];
        
        // Filtreler
        if (!empty($filters['model_kodu'])) {
            $sql .= " AND m.model_kodu LIKE :model_kodu";
            $params['model_kodu'] = '%' . $filters['model_kodu'] . '%';
        }
        
        if (!empty($filters['siparis_numarasi'])) {
            $sql .= " AND o.siparis_numarasi LIKE :siparis_numarasi";
            $params['siparis_numarasi'] = '%' . $filters['siparis_numarasi'] . '%';
        }
        
        if (!empty($filters['teslimat_ulkesi'])) {
            $sql .= " AND o.teslimat_ulkesi = :teslimat_ulkesi";
            $params['teslimat_ulkesi'] = $filters['teslimat_ulkesi'];
        }
        
        if (!empty($filters['durum'])) {
            $sql .= " AND bl.label_status = :durum";
            $params['durum'] = $filters['durum'];
        }
        
        if (!empty($filters['tarih_baslangic'])) {
            $sql .= " AND bl.created_at >= :tarih_baslangic";
            $params['tarih_baslangic'] = $filters['tarih_baslangic'] . ' 00:00:00';
        }
        
        if (!empty($filters['tarih_bitis'])) {
            $sql .= " AND bl.created_at <= :tarih_bitis";
            $params['tarih_bitis'] = $filters['tarih_bitis'] . ' 23:59:59';
        }
        
        $sql .= " ORDER BY bl.created_at DESC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Teslimat raporu
     * 
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    public function getDeliveryReport($filters) {
        $sql = "SELECT o.*, 
                m.model_kodu,
                (o.siparis_gecilen_lot_sayisi - o.depo_girisi_olan_lot_sayisi) as kalan_lot,
                (o.siparis_gecilen_acik_adet_sayisi - o.depo_girisi_olan_acik_adet_sayisi) as kalan_adet,
                (SELECT COUNT(*) FROM boxes b WHERE b.siparis_id = o.id) as box_count
                FROM orders o
                JOIN models m ON o.model_id = m.id
                WHERE 1=1";
        
        $params = [];
        
        // Filtreler
        if (!empty($filters['model_kodu'])) {
            $sql .= " AND m.model_kodu LIKE :model_kodu";
            $params['model_kodu'] = '%' . $filters['model_kodu'] . '%';
        }
        
        if (!empty($filters['siparis_numarasi'])) {
            $sql .= " AND o.siparis_numarasi LIKE :siparis_numarasi";
            $params['siparis_numarasi'] = '%' . $filters['siparis_numarasi'] . '%';
        }
        
        if (!empty($filters['teslimat_ulkesi'])) {
            $sql .= " AND o.teslimat_ulkesi = :teslimat_ulkesi";
            $params['teslimat_ulkesi'] = $filters['teslimat_ulkesi'];
        }
        
        if (!empty($filters['durum'])) {
            if ($filters['durum'] == 'completed') {
                $sql .= " AND o.siparis_gecilen_lot_sayisi = o.depo_girisi_olan_lot_sayisi";
            } elseif ($filters['durum'] == 'pending') {
                $sql .= " AND o.siparis_gecilen_lot_sayisi > o.depo_girisi_olan_lot_sayisi";
            }
        }
        
        if (!empty($filters['tarih_baslangic'])) {
            $sql .= " AND o.created_at >= :tarih_baslangic";
            $params['tarih_baslangic'] = $filters['tarih_baslangic'] . ' 00:00:00';
        }
        
        if (!empty($filters['tarih_bitis'])) {
            $sql .= " AND o.created_at <= :tarih_bitis";
            $params['tarih_bitis'] = $filters['tarih_bitis'] . ' 23:59:59';
        }
        
        $sql .= " ORDER BY o.teslimat_ulkesi ASC, o.siparis_numarasi ASC";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Tamamlanan siparişleri getir
     * 
     * @param array $filters Filtreler
     * @return array Siparişler
     */
    private function getCompletedOrders($filters) {
        $sql = "SELECT o.*, 
                m.model_kodu
                FROM orders o
                JOIN models m ON o.model_id = m.id
                WHERE o.siparis_gecilen_lot_sayisi = o.depo_girisi_olan_lot_sayisi";
        
        $params = [];
        
        // Filtreler
        if (!empty($filters['model_kodu'])) {
            $sql .= " AND m.model_kodu LIKE :model_kodu";
            $params['model_kodu'] = '%' . $filters['model_kodu'] . '%';
        }
        
        if (!empty($filters['siparis_numarasi'])) {
            $sql .= " AND o.siparis_numarasi LIKE :siparis_numarasi";
            $params['siparis_numarasi'] = '%' . $filters['siparis_numarasi'] . '%';
        }
        
        if (!empty($filters['teslimat_ulkesi'])) {
            $sql .= " AND o.teslimat_ulkesi = :teslimat_ulkesi";
            $params['teslimat_ulkesi'] = $filters['teslimat_ulkesi'];
        }
        
        if (!empty($filters['tarih_baslangic'])) {
            $sql .= " AND o.created_at >= :tarih_baslangic";
            $params['tarih_baslangic'] = $filters['tarih_baslangic'] . ' 00:00:00';
        }
        
        if (!empty($filters['tarih_bitis'])) {
            $sql .= " AND o.created_at <= :tarih_bitis";
            $params['tarih_bitis'] = $filters['tarih_bitis'] . ' 23:59:59';
        }
        
        $sql .= " ORDER BY o.teslimat_ulkesi ASC, o.siparis_numarasi ASC LIMIT 10";
        
        return $this->query($sql, $params);
    }
    
    /**
     * Bekleyen siparişleri getir
     * 
     * @param array $filters Filtreler
     * @return array Siparişler
     */
    private function getPendingOrders($filters) {
        $sql = "SELECT o.*, 
                m.model_kodu,
                (o.siparis_gecilen_lot_sayisi - o.depo_girisi_olan_lot_sayisi) as kalan_lot,
                (o.siparis_gecilen_acik_adet_sayisi - o.depo_girisi_olan_acik_adet_sayisi) as kalan_adet
                FROM orders o
                JOIN models m ON o.model_id = m.id
                WHERE o.siparis_gecilen_lot_sayisi > o.depo_girisi_olan_lot_sayisi";
        
        $params = [];
        
        // Filtreler
        if (!empty($filters['model_kodu'])) {
            $sql .= " AND m.model_kodu LIKE :model_kodu";
            $params['model_kodu'] = '%' . $filters['model_kodu'] . '%';
        }
        
        if (!empty($filters['siparis_numarasi'])) {
            $sql .= " AND o.siparis_numarasi LIKE :siparis_numarasi";
            $params['siparis_numarasi'] = '%' . $filters['siparis_numarasi'] . '%';
        }
        
        if (!empty($filters['teslimat_ulkesi'])) {
            $sql .= " AND o.teslimat_ulkesi = :teslimat_ulkesi";
            $params['teslimat_ulkesi'] = $filters['teslimat_ulkesi'];
        }
        
        if (!empty($filters['tarih_baslangic'])) {
            $sql .= " AND o.created_at >= :tarih_baslangic";
            $params['tarih_baslangic'] = $filters['tarih_baslangic'] . ' 00:00:00';
        }
        
        if (!empty($filters['tarih_bitis'])) {
            $sql .= " AND o.created_at <= :tarih_bitis";
            $params['tarih_bitis'] = $filters['tarih_bitis'] . ' 23:59:59';
        }
        
        $sql .= " ORDER BY o.teslimat_ulkesi ASC, o.siparis_numarasi ASC LIMIT 10";
        
        return $this->query($sql, $params);
    }
}