<?php
/**
 * Koli için model sınıfı
 */
class BoxModel extends Model {
    protected $tableName = 'boxes';
    
    /**
     * Koli istatistiklerini getir
     * 
     * @return array İstatistikler
     */
    public function getStatistics() {
        // Toplam koli sayısı
        $this->db->query("SELECT COUNT(*) as total FROM {$this->tableName}");
        $totalBoxes = $this->db->fetch()['total'];
        
        // Tam koli sayısı
        $this->db->query("SELECT COUNT(*) as total FROM {$this->tableName} WHERE box_type = 'tam'");
        $fullBoxes = $this->db->fetch()['total'];
        
        // Kırık koli sayısı
        $this->db->query("SELECT COUNT(*) as total FROM {$this->tableName} WHERE box_type = 'kırık'");
        $partialBoxes = $this->db->fetch()['total'];
        
        // Durum dağılımı
        $this->db->query("SELECT status, COUNT(*) as count FROM {$this->tableName} GROUP BY status ORDER BY count DESC");
        $statusDistribution = $this->db->fetchAll();
        
        return [
            'total_boxes' => $totalBoxes,
            'full_boxes' => $fullBoxes,
            'partial_boxes' => $partialBoxes,
            'status_distribution' => $statusDistribution
        ];
    }
    
    /**
     * Koli hesaplaması yap
     * 
     * @param array $order Sipariş bilgileri
     * @param int $koliLotSayisi Koli lot sayısı
     * @param int|null $remainingLots Kalan lot sayısı
     * @return array Hesaplama sonuçları
     */
    public function calculateBoxes($order, $koliLotSayisi, $remainingLots = null) {
        // Kalan lot sayısı belirtilmemişse sipariş lot sayısını kullan
        if ($remainingLots === null) {
            $remainingLots = $order['siparis_gecilen_lot_sayisi'] - $order['depo_girisi_olan_lot_sayisi'];
        }
        
        // Hesaplama sonuçları
        $result = [
            'siparis_lot' => $order['siparis_gecilen_lot_sayisi'],
            'depo_giris' => $order['depo_girisi_olan_lot_sayisi'],
            'kalan_lot' => $remainingLots,
            'koli_lot_sayisi' => $koliLotSayisi,
            'tam_koli_sayisi' => 0,
            'tam_koli_lot_toplami' => 0,
            'kirik_koli_lot_sayisi' => 0,
            'toplam_lot' => 0,
            'toplam_adet' => 0,
            'durum' => '',
            'tam_koliler' => [],
            'kirik_koli' => null
        ];
        
        // Teslimat tamamlanmış mı kontrol et
        if ($remainingLots <= 0) {
            $result['durum'] = 'TESLİMAT TAMAMLANMIŞ';
            return $result;
        }
        
        // Tam koli sayısını hesapla
        $result['tam_koli_sayisi'] = floor($remainingLots / $koliLotSayisi);
        $result['tam_koli_lot_toplami'] = $result['tam_koli_sayisi'] * $koliLotSayisi;
        
        // Kırık koli lot sayısını hesapla
        $result['kirik_koli_lot_sayisi'] = $remainingLots % $koliLotSayisi;
        
        // Toplam lot ve adet
        $result['toplam_lot'] = $result['tam_koli_lot_toplami'] + $result['kirik_koli_lot_sayisi'];
        $result['toplam_adet'] = $result['toplam_lot'] * $order['bir_lottaki_urun_sayisi'];
        
        // Tam kolileri oluştur
        for ($i = 1; $i <= $result['tam_koli_sayisi']; $i++) {
            $result['tam_koliler'][] = [
                'box_number' => $i,
                'lot_count' => $koliLotSayisi,
                'adet' => $koliLotSayisi * $order['bir_lottaki_urun_sayisi']
            ];
        }
        
        // Kırık koli varsa oluştur
        if ($result['kirik_koli_lot_sayisi'] > 0) {
            $result['kirik_koli'] = [
                'box_number' => $result['tam_koli_sayisi'] + 1,
                'lot_count' => $result['kirik_koli_lot_sayisi'],
                'adet' => $result['kirik_koli_lot_sayisi'] * $order['bir_lottaki_urun_sayisi']
            ];
        }
        
        return $result;
    }
    
    /**
     * Koli hesaplamasını kaydet
     * 
     * @param array $calculation Hesaplama sonuçları
     * @param int $orderId Sipariş ID
     * @param int $modelId Model ID
     * @return array Oluşturulan koli ID'leri
     */
    public function saveCalculation($calculation, $orderId, $modelId) {
        $boxIds = [];
        
        // İşlemi başlat
        $this->db->beginTransaction();
        
        try {
            // Tam kolileri kaydet
            foreach ($calculation['tam_koliler'] as $box) {
                $boxId = $this->create([
                    'model_id' => $modelId,
                    'siparis_id' => $orderId,
                    'box_type' => 'tam',
                    'lot_count' => $box['lot_count'],
                    'box_number' => $box['box_number'],
                    'status' => 'hazırlanıyor'
                ]);
                
                $boxIds[] = $boxId;
                
                // Koli etiketi oluştur
                $this->createBoxLabel($boxId);
            }
            
            // Kırık koli varsa kaydet
            if ($calculation['kirik_koli']) {
                $boxId = $this->create([
                    'model_id' => $modelId,
                    'siparis_id' => $orderId,
                    'box_type' => 'kırık',
                    'lot_count' => $calculation['kirik_koli']['lot_count'],
                    'box_number' => $calculation['kirik_koli']['box_number'],
                    'status' => 'hazırlanıyor'
                ]);
                
                $boxIds[] = $boxId;
                
                // Koli etiketi oluştur
                $this->createBoxLabel($boxId);
            }
            
            // İşlemi tamamla
            $this->db->commit();
            
            return $boxIds;
        } catch (Exception $e) {
            // Hata durumunda işlemi geri al
            $this->db->rollback();
            throw $e;
        }
    }
    
    /**
     * Koli etiketi oluştur
     * 
     * @param int $boxId Koli ID
     * @return int|false Eklenen kaydın ID'si
     */
    private function createBoxLabel($boxId) {
        $this->db->query("INSERT INTO box_labels (box_id, label_status) VALUES (:box_id, 'indirilmedi')");
        $this->db->bind(':box_id', $boxId);
        
        $this->db->execute();
        return $this->db->lastInsertId();
    }
    
    /**
     * Sipariş ID'sine göre kolileri getir
     * 
     * @param int $orderId Sipariş ID
     * @return array Koli listesi
     */
    public function getBoxesByOrderId($orderId) {
        $this->db->query("SELECT b.*, 
                        (SELECT label_status FROM box_labels bl WHERE bl.box_id = b.id ORDER BY bl.created_at DESC LIMIT 1) as label_status 
                        FROM {$this->tableName} b 
                        WHERE b.siparis_id = :order_id 
                        ORDER BY b.box_number");
        $this->db->bind(':order_id', $orderId);
        return $this->db->fetchAll();
    }
    
    /**
     * Model ID'sine göre kolileri getir
     * 
     * @param int $modelId Model ID
     * @return array Koli listesi
     */
    public function getBoxesByModelId($modelId) {
        $this->db->query("SELECT b.*, 
                        o.siparis_numarasi, o.teslimat_ulkesi, o.lot_kodu,
                        (SELECT label_status FROM box_labels bl WHERE bl.box_id = b.id ORDER BY bl.created_at DESC LIMIT 1) as label_status 
                        FROM {$this->tableName} b 
                        JOIN orders o ON b.siparis_id = o.id
                        WHERE b.model_id = :model_id 
                        ORDER BY o.siparis_numarasi, b.box_number");
        $this->db->bind(':model_id', $modelId);
        return $this->db->fetchAll();
    }
    
    /**
     * Koli durumunu güncelle
     * 
     * @param int $boxId Koli ID
     * @param string $status Durum
     * @return bool Başarı durumu
     */
    public function updateStatus($boxId, $status) {
        return $this->update($boxId, [
            'status' => $status
        ]);
    }
    
    /**
     * Koli etiket durumunu güncelle
     * 
     * @param int $boxId Koli ID
     * @param string $labelStatus Etiket durumu
     * @param array $additionalData Ek veriler
     * @return bool Başarı durumu
     */
    public function updateLabelStatus($boxId, $labelStatus, $additionalData = []) {
        $data = [
            'box_id' => $boxId,
            'label_status' => $labelStatus
        ];
        
        // Ek verileri ekle
        if (isset($additionalData['teslim_edilen_kisi'])) {
            $data['teslim_edilen_kisi'] = $additionalData['teslim_edilen_kisi'];
        }
        
        if (isset($additionalData['teslim_tarihi'])) {
            $data['teslim_tarihi'] = $additionalData['teslim_tarihi'];
        }
        
        if (isset($additionalData['teslim_adet'])) {
            $data['teslim_adet'] = $additionalData['teslim_adet'];
        }
        
        if (isset($additionalData['notes'])) {
            $data['notes'] = $additionalData['notes'];
        }
        
        $this->db->query("INSERT INTO box_labels (box_id, label_status, teslim_edilen_kisi, teslim_tarihi, teslim_adet, notes) 
                        VALUES (:box_id, :label_status, :teslim_edilen_kisi, :teslim_tarihi, :teslim_adet, :notes)");
        
        $this->db->bind(':box_id', $data['box_id']);
        $this->db->bind(':label_status', $data['label_status']);
        $this->db->bind(':teslim_edilen_kisi', $data['teslim_edilen_kisi'] ?? null);
        $this->db->bind(':teslim_tarihi', $data['teslim_tarihi'] ?? null);
        $this->db->bind(':teslim_adet', $data['teslim_adet'] ?? null);
        $this->db->bind(':notes', $data['notes'] ?? null);
        
        return $this->db->execute();
    }
}