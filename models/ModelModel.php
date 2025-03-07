<?php
/**
 * Model kodları için model sınıfı
 */
class ModelModel extends Model {
    protected $tableName = 'models';
    
    /**
     * Model istatistiklerini getir
     * 
     * @return array İstatistikler
     */
    public function getStatistics() {
        // Toplam model sayısı
        $this->db->query("SELECT COUNT(*) as total FROM {$this->tableName}");
        $totalModels = $this->db->fetch()['total'];
        
        // Resimli model sayısı
        $this->db->query("SELECT COUNT(*) as total FROM {$this->tableName} WHERE image_path IS NOT NULL");
        $withImages = $this->db->fetch()['total'];
        
        // Son 30 gün içinde eklenen model sayısı
        $this->db->query("SELECT COUNT(*) as total FROM {$this->tableName} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $lastMonth = $this->db->fetch()['total'];
        
        return [
            'total' => $totalModels,
            'with_images' => $withImages,
            'last_month' => $lastMonth
        ];
    }
    
    /**
     * Son eklenen modelleri getir
     * 
     * @param int $limit Limit
     * @return array Model listesi
     */
    public function getRecent($limit = 5) {
        $this->db->query("SELECT m.*, 
                         (SELECT COUNT(*) FROM orders o WHERE o.model_id = m.id) as order_count 
                         FROM {$this->tableName} m 
                         ORDER BY m.created_at DESC 
                         LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->fetchAll();
    }
    
    /**
     * Model koduna göre model getir
     * 
     * @param string $modelKodu Model kodu
     * @return array|false Model bilgileri
     */
    public function getByModelKodu($modelKodu) {
        $this->db->query("SELECT * FROM {$this->tableName} WHERE model_kodu = :model_kodu");
        $this->db->bind(':model_kodu', $modelKodu);
        return $this->db->fetch();
    }
    
    /**
     * Model kodunu kontrol et, yoksa oluştur
     * 
     * @param string $modelKodu Model kodu
     * @return int Model ID
     */
    public function getOrCreateByModelKodu($modelKodu) {
        $model = $this->getByModelKodu($modelKodu);
        
        if ($model) {
            return $model['id'];
        }
        
        // Yeni model oluştur
        $modelId = $this->create([
            'model_kodu' => $modelKodu
        ]);
        
        return $modelId;
    }
    
    /**
     * Modele resim ekle
     * 
     * @param int $modelId Model ID
     * @param string $imagePath Resim yolu
     * @return bool Başarı durumu
     */
    public function addImage($modelId, $imagePath) {
        return $this->update($modelId, [
            'image_path' => $imagePath
        ]);
    }
    
    /**
     * Tüm modelleri detayları ile getir
     * 
     * @return array Model listesi
     */
    public function getAllWithDetails() {
        $this->db->query("SELECT m.*, 
                         (SELECT COUNT(*) FROM orders o WHERE o.model_id = m.id) as order_count,
                         (SELECT COUNT(*) FROM boxes b WHERE b.model_id = m.id) as box_count
                         FROM {$this->tableName} m 
                         ORDER BY m.model_kodu ASC");
        return $this->db->fetchAll();
    }
}