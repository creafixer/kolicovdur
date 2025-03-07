<?php
/**
 * Veritabanı Sınıfı
 */
class Database {
    private $connection;
    private $statement;
    private $error;
    
    /**
     * Veritabanı bağlantısını kur
     */
    public function __construct() {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        
        try {
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo 'Veritabanı Bağlantı Hatası: ' . $this->error;
        }
    }
    
    /**
     * SQL sorgusu hazırla
     * 
     * @param string $sql SQL sorgusu
     * @return void
     */
    public function query($sql) {
        $this->statement = $this->connection->prepare($sql);
    }
    
    /**
     * Sorgu parametrelerini bağla
     * 
     * @param string $param Parametre adı
     * @param mixed $value Parametre değeri
     * @param mixed $type Parametre tipi
     * @return void
     */
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        
        $this->statement->bindValue($param, $value, $type);
    }
    
    /**
     * Sorguyu çalıştır
     * 
     * @return bool
     */
    public function execute() {
        return $this->statement->execute();
    }
    
    /**
     * Tüm sonuçları getir
     * 
     * @return array
     */
    public function fetchAll() {
        $this->execute();
        return $this->statement->fetchAll();
    }
    
    /**
     * Tek bir sonuç getir
     * 
     * @return object
     */
    public function fetch() {
        $this->execute();
        return $this->statement->fetch();
    }
    
    /**
     * Etkilenen satır sayısını getir
     * 
     * @return int
     */
    public function rowCount() {
        return $this->statement->rowCount();
    }
    
    /**
     * Son eklenen kaydın ID'sini getir
     * 
     * @return int
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * İşlem başlat
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * İşlemi tamamla
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * İşlemi geri al
     */
    public function rollback() {
        return $this->connection->rollBack();
    }
    
    /**
     * Veritabanı tablosu oluştur
     * 
     * @param string $tableName Tablo adı
     * @param array $columns Sütun tanımları
     * @return bool
     */
    public function createTable($tableName, $columns) {
        $sql = "CREATE TABLE IF NOT EXISTS $tableName (";
        $sql .= implode(', ', $columns);
        $sql .= ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->query($sql);
        return $this->execute();
    }
    
    /**
     * Tüm tabloları oluştur
     */
    public function createAllTables() {
        $tables = require CONFIG_DIR . '/database.php';
        
        foreach ($tables as $tableName => $columns) {
            $this->createTable($tableName, $columns);
        }
    }
}