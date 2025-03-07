<?php
/**
 * Excel Veri İşleme Sınıfı
 */
class ExcelParser {
    /**
     * Excel verisini ayrıştır
     * 
     * @param string $excelData Excel verisi
     * @return array Ayrıştırılmış veri
     */
    public function parseExcelData($excelData) {
        try {
            // Satırları ayır
            $rows = explode("\n", trim($excelData));
            
            if (count($rows) < 5) {
                return [
                    'success' => false,
                    'error' => 'Geçersiz veri formatı. Yeterli satır bulunamadı.'
                ];
            }
            
            // Tabloları tespit et
            $tables = $this->detectTables($rows);
            
            if (!isset($tables['toplam_siparis']) || !isset($tables['beden_bazli'])) {
                return [
                    'success' => false,
                    'error' => 'Gerekli tablolar bulunamadı. "Toplam Sipariş" ve "Beden Bazlı Toplam Sipariş" tabloları gereklidir.'
                ];
            }
            
            // Tabloları ayrıştır
            $parsedTotalOrders = $this->parseTotalOrderTable($tables['toplam_siparis']);
            $parsedSizeBasedOrders = $this->parseSizeBasedTable($tables['beden_bazli']);
            
            // Sonuçları döndür
            return [
                'success' => true,
                'data' => [
                    'toplam_siparis' => $parsedTotalOrders,
                    'beden_bazli' => $parsedSizeBasedOrders
                ]
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Veri ayrıştırma hatası: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Tabloları tespit et
     * 
     * @param array $rows Satırlar
     * @return array Tablolar
     */
    private function detectTables($rows) {
        $tables = [
            'toplam_siparis' => [],
            'beden_bazli' => []
        ];
        
        $currentTable = null;
        
        foreach ($rows as $row) {
            $row = trim($row);
            
            if (empty($row)) {
                continue;
            }
            
            if (strpos($row, 'Toplam Sipariş') === 0) {
                $currentTable = 'toplam_siparis';
                $tables[$currentTable][] = $row;
            } elseif (strpos($row, 'Beden Bazlı Toplam Sipariş') === 0) {
                $currentTable = 'beden_bazli';
                $tables[$currentTable][] = $row;
            } elseif ($currentTable !== null) {
                $tables[$currentTable][] = $row;
            }
        }
        
        return $tables;
    }
    
    /**
     * Toplam Sipariş tablosunu ayrıştır
     * 
     * @param array $rows Tablo satırları
     * @return array Ayrıştırılmış veri
     */
    private function parseTotalOrderTable($rows) {
        if (count($rows) < 3) {
            return [];
        }
        
        // Başlık satırını al
        $headerRow = $rows[1];
        $headers = $this->splitRow($headerRow);
        
        // Veri satırlarını işle
        $data = [];
        for ($i = 2; $i < count($rows); $i++) {
            $row = $rows[$i];
            $values = $this->splitRow($row);
            
            // Satır ve başlık sayısını kontrol et
            if (count($values) < count($headers)) {
                continue;
            }
            
            $rowData = [];
            $bedenler = [];
            
            // Beden sütunlarını tespit et
            $bedenStartIndex = 8; // "Set İçeriği" sütunundan sonraki sütun
            $bedenEndIndex = array_search('Bir Lottaki Ürün Sayısı', $headers);
            
            if ($bedenEndIndex === false) {
                $bedenEndIndex = array_search('Teslimat Ülkesi', $headers) - 1;
            }
            
            // Standart sütunları ekle
            foreach ($headers as $index => $header) {
                // Beden sütunları
                if ($index >= $bedenStartIndex && $index < $bedenEndIndex) {
                    $bedenAdi = $header;
                    $bedenValue = $values[$index];
                    
                    // "-" değerini 0 olarak işle
                    if ($bedenValue === '-') {
                        $bedenValue = 0;
                    }
                    
                    $bedenler[$bedenAdi] = intval($bedenValue);
                    $rowData[$bedenAdi] = $bedenValue;
                } else {
                    $rowData[$header] = $values[$index];
                }
            }
            
            // Bedenler bilgisini ekle
            $rowData['bedenler'] = $bedenler;
            
            $data[] = $rowData;
        }
        
        return $data;
    }
    
    /**
     * Beden Bazlı Toplam Sipariş tablosunu ayrıştır
     * 
     * @param array $rows Tablo satırları
     * @return array Ayrıştırılmış veri
     */
    private function parseSizeBasedTable($rows) {
        if (count($rows) < 3) {
            return [];
        }
        
        // Başlık satırını al
        $headerRow = $rows[1];
        $headers = $this->splitRow($headerRow);
        
        // Veri satırlarını işle
        $data = [];
        for ($i = 2; $i < count($rows); $i++) {
            $row = $rows[$i];
            $values = $this->splitRow($row);
            
            // Satır ve başlık sayısını kontrol et
            if (count($values) < count($headers)) {
                continue;
            }
            
            $rowData = [];
            
            foreach ($headers as $index => $header) {
                $rowData[$header] = $values[$index];
            }
            
            $data[] = $rowData;
        }
        
        return $data;
    }
    
    /**
     * Satırı böl
     * 
     * @param string $row Satır
     * @return array Bölünmüş değerler
     */
    private function splitRow($row) {
        return array_map('trim', explode("\t", $row));
    }
}