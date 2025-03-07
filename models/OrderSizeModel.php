<?php
/**
 * Sipariş bedenleri için model sınıfı
 */
class OrderSizeModel extends Model {
    protected $tableName = 'order_sizes';
    
    /**
     * Sipariş ID'sine göre bedenleri getir
     * 
     * @param int $orderId Sipariş ID
     * @return array Beden listesi
     */
    public function getByOrderId($orderId) {
        $this->db->query("SELECT * FROM {$this->tableName} WHERE order_id = :order_id");
        $this->db->bind(':order_id', $orderId);
        return $this->db->fetchAll();
    }
    
    /**
     * Beden oluştur veya güncelle
     * 
     * @param int $orderId Sipariş ID
     * @param string $bedenAdi Beden adı
     * @param int $adet Adet
     * @return int|bool Eklenen kaydın ID'si veya güncelleme durumu
     */
    public function createOrUpdate($orderId, $bedenAdi, $adet) {
        // Beden var mı kontrol et
        $this->db->query("SELECT id FROM {$this->tableName} WHERE order_id = :order_id AND beden_adi = :beden_adi");
        $this->db->bind(':order_id', $orderId);
        $this->db->bind(':beden_adi', $bedenAdi);
        $existingSize = $this->db->fetch();
        
        if ($existingSize) {
            // Bedeni güncelle
            return $this->update($existingSize['id'], [
                'adet' => $adet
            ]);
        } else {
            // Yeni beden oluştur
            return $this->create([
                'order_id' => $orderId,
                'beden_adi' => $bedenAdi,
                'adet' => $adet
            ]);
        }
    }
    
    /**
     * Sipariş ID'sine göre bedenleri sil
     * 
     * @param int $orderId Sipariş ID
     * @return bool Başarı durumu
     */
    public function deleteByOrderId($orderId) {
        $this->db->query("DELETE FROM {$this->tableName} WHERE order_id = :order_id");
        $this->db->bind(':order_id', $orderId);
        return $this->db->execute();
    }
    
    /**
     * Sipariş ID'sine göre bedenleri dizi olarak getir
     * 
     * @param int $orderId Sipariş ID
     * @return array Beden => Adet formatında dizi
     */
    public function getByOrderIdAsArray($orderId) {
        $this->db->query("SELECT beden_adi, adet FROM {$this->tableName} WHERE order_id = :order_id");
        $this->db->bind(':order_id', $orderId);
        $sizes = $this->db->fetchAll();
        
        $result = [];
        foreach ($sizes as $size) {
            $result[$size['beden_adi']] = $size['adet'];
        }
        
        return $result;
    }
}