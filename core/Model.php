<?php
/**
 * Temel Model Sınıfı
 */
class Model {
    protected $db;
    protected $tableName;
    
    /**
     * Model yapıcı metodu
     */
    public function __construct() {
        $this->db = App::getInstance()->getDB();
    }
    
    /**
     * Tüm kayıtları getir
     * 
     * @return array
     */
    public function getAll() {
        $this->db->query("SELECT * FROM {$this->tableName}");
        return $this->db->fetchAll();
    }
    
    /**
     * ID'ye göre kayıt getir
     * 
     * @param int $id Kayıt ID'si
     * @return array|false
     */
    public function getById($id) {
        $this->db->query("SELECT * FROM {$this->tableName} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->fetch();
    }
    
    /**
     * Yeni kayıt ekle
     * 
     * @param array $data Eklenecek veriler
     * @return int|false Eklenen kaydın ID'si veya başarısız ise false
     */
    public function create($data) {
        // SQL sorgusunu hazırla
        $fields = array_keys($data);
        $placeholders = array_map(function($field) {
            return ':' . $field;
        }, $fields);
        
        $sql = "INSERT INTO {$this->tableName} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";
        
        $this->db->query($sql);
        
        // Parametreleri bağla
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        // Sorguyu çalıştır
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Kaydı güncelle
     * 
     * @param int $id Güncellenecek kaydın ID'si
     * @param array $data Güncellenecek veriler
     * @return bool
     */
    public function update($id, $data) {
        // SQL sorgusunu hazırla
        $setFields = array_map(function($field) {
            return $field . ' = :' . $field;
        }, array_keys($data));
        
        $sql = "UPDATE {$this->tableName} SET " . implode(', ', $setFields) . " WHERE id = :id";
        
        $this->db->query($sql);
        
        // Parametreleri bağla
        $this->db->bind(':id', $id);
        foreach ($data as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        // Sorguyu çalıştır
        return $this->db->execute();
    }
    
    /**
     * Kaydı sil
     * 
     * @param int $id Silinecek kaydın ID'si
     * @return bool
     */
    public function delete($id) {
        $this->db->query("DELETE FROM {$this->tableName} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    /**
     * Özel sorgu çalıştır
     * 
     * @param string $sql SQL sorgusu
     * @param array $params Sorgu parametreleri
     * @return array
     */
    public function query($sql, $params = []) {
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        return $this->db->fetchAll();
    }
    
    /**
     * Tek sonuç döndüren özel sorgu
     * 
     * @param string $sql SQL sorgusu
     * @param array $params Sorgu parametreleri
     * @return array|false
     */
    public function queryOne($sql, $params = []) {
        $this->db->query($sql);
        
        foreach ($params as $key => $value) {
            $this->db->bind(':' . $key, $value);
        }
        
        return $this->db->fetch();
    }
}