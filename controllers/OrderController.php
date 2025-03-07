<?php
/**
 * Sipariş Controller
 */
class OrderController extends Controller {
    /**
     * Excel veri içe aktarma formu
     */
    public function importForm() {
        $this->render('orders/import', [
            'pageTitle' => 'Excel Verisi İçe Aktar',
            'activePage' => 'orders-import'
        ]);
    }
    
    /**
     * Excel verilerini işle
     */
    public function processImport() {
        // POST verilerini al
        $excelData = $this->post('excel_data');
        $confirmed = $this->post('confirmed', false);
        
        if (!$excelData) {
            $this->json([
                'success' => false,
                'error' => 'Excel verisi bulunamadı'
            ]);
        }
        
        // Excel verilerini ayrıştır
        $parsedData = $this->parseExcelData($excelData);
        
        if ($confirmed) {
            // Verileri kaydet
            try {
                $orderModel = new OrderModel();
                $orderIds = $orderModel->createFromExcel($parsedData);
                
                $this->json([
                    'success' => true,
                    'message' => count($orderIds) . ' adet sipariş başarıyla kaydedildi',
                    'order_ids' => $orderIds
                ]);
            } catch (Exception $e) {
                $this->json([
                    'success' => false,
                    'error' => 'Veriler kaydedilirken hata oluştu: ' . $e->getMessage()
                ]);
            }
        } else {
            // Önizleme için verileri döndür
            $this->json([
                'success' => true,
                'data' => $parsedData,
                'preview' => $this->generatePreview($parsedData)
            ]);
        }
    }
    
    /**
     * Excel verilerini ayrıştır
     * 
     * @param string $data Excel verisi
     * @return array Ayrıştırılmış veriler
     */
    private function parseExcelData($data) {
        // Satırları ayır
        $rows = explode("\n", trim($data));
        
        // Tabloları tespit et
        $tables = $this->detectTables($rows);
        
        // Tabloları ayrıştır
        return [
            'toplam_siparis' => $this->parseTotalOrderTable($tables['toplam_siparis']),
            'beden_bazli' => $this->parseSizeBasedTable($tables['beden_bazli'])
        ];
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
        
        foreach ($rows as $index => $row) {
            if (strpos($row, 'Toplam Sipariş') === 0) {
                $currentTable = 'toplam_siparis';
                $tables[$currentTable][] = $row;
            } elseif (strpos($row, 'Beden Bazlı Toplam Sipariş') === 0) {
                $currentTable = 'beden_bazli';
                $tables[$currentTable][] = $row;
            } elseif ($currentTable && trim($row) !== '') {
                $tables[$currentTable][] = $row;
            }
        }
        
        return $tables;
    }
    
    /**
     * Toplam sipariş tablosunu ayrıştır
     * 
     * @param array $rows Tablo satırları
     * @return array Ayrıştırılmış veriler
     */
    private function parseTotalOrderTable($rows) {
        $result = [];
        
        // En az 3 satır olmalı (başlık, sütun adları, veri)
        if (count($rows) < 3) {
            return $result;
        }
        
        // Sütun adlarını al (2. satır)
        $headers = explode("\t", $rows[1]);
        
        // Beden sütunlarını belirle
        $sizeColumns = [];
        $sizeColumnStart = 8; // "Set İçeriği" sütunundan sonra
        $sizeColumnEnd = 0;
        
        for ($i = $sizeColumnStart; $i < count($headers); $i++) {
            if ($headers[$i] === 'Bir Lottaki Ürün Sayısı') {
                $sizeColumnEnd = $i - 1;
                break;
            }
            
            $sizeColumns[] = $headers[$i];
        }
        
        // Veri satırlarını işle
        for ($i = 2; $i < count($rows); $i++) {
            $row = explode("\t", $rows[$i]);
            
            // Boş satırları atla
            if (count($row) < count($headers)) {
                continue;
            }
            
            // Beden verilerini topla
            $sizes = [];
            for ($j = 0; $j < count($sizeColumns); $j++) {
                $sizeIndex = $sizeColumnStart + $j;
                $sizeValue = trim($row[$sizeIndex]);
                
                // "-" işareti yerine 0 kullan
                $sizeValue = ($sizeValue === '-') ? 0 : intval($sizeValue);
                
                $sizes[$sizeColumns[$j]] = $sizeValue;
            }
            
            // Sipariş verisini oluştur
            $orderData = [
                'model_kodu' => $row[0],
                'sezon' => $row[1],
                'siparis_numarasi' => $row[2],
                'ship_to' => $row[3],
                'tedarikci_termini' => $this->formatDate($row[4]),
                'renk_kodu_adi' => $row[5],
                'lot_kodu' => $row[6],
                'set_icerigi' => $row[7],
                'bedenler' => $sizes,
                'bir_lottaki_urun_sayisi' => intval($row[$sizeColumnEnd + 1]),
                'teslimat_ulkesi' => $row[$sizeColumnEnd + 2],
                'siparis_gecilen_lot_sayisi' => intval($row[$sizeColumnEnd + 3]),
                'siparis_gecilen_acik_adet_sayisi' => intval($row[$sizeColumnEnd + 4]),
                'depo_girisi_olan_lot_sayisi' => isset($row[$sizeColumnEnd + 5]) ? intval($row[$sizeColumnEnd + 5]) : 0,
                'depo_girisi_olan_acik_adet_sayisi' => isset($row[$sizeColumnEnd + 6]) ? intval($row[$sizeColumnEnd + 6]) : 0
            ];
            
            $result[] = $orderData;
        }
        
        return $result;
    }
    
    /**
     * Beden bazlı toplam sipariş tablosunu ayrıştır
     * 
     * @param array $rows Tablo satırları
     * @return array Ayrıştırılmış veriler
     */
    private function parseSizeBasedTable($rows) {
        $result = [];
        
        // En az 3 satır olmalı (başlık, sütun adları, veri)
        if (count($rows) < 3) {
            return $result;
        }
        
        // Sütun adlarını al (2. satır)
        $headers = explode("\t", $rows[1]);
        
        // Beden sütunlarını belirle
        $sizeColumns = [];
        $sizeColumnStart = 8; // "Set İçeriği" sütunundan sonra
        $sizeColumnEnd = 0;
        
        for ($i = $sizeColumnStart; $i < count($headers); $i++) {
            if ($headers[$i] === 'Teslimat Ülkesi') {
                $sizeColumnEnd = $i - 1;
                break;
            }
            
            $sizeColumns[] = $headers[$i];
        }
        
        // Veri satırlarını işle
        for ($i = 2; $i < count($rows); $i++) {
            $row = explode("\t", $rows[$i]);
            
            // Boş satırları atla
            if (count($row) < count($headers)) {
                continue;
            }
            
            // Beden verilerini topla
            $sizes = [];
            for ($j = 0; $j < count($sizeColumns); $j++) {
                $sizeIndex = $sizeColumnStart + $j;
                $sizeValue = trim($row[$sizeIndex]);
                
                // "-" işareti yerine 0 kullan
                $sizeValue = ($sizeValue === '-') ? 0 : intval($sizeValue);
                
                $sizes[$sizeColumns[$j]] = $sizeValue;
            }
            
            // Sipariş verisini oluştur
            $orderData = [
                'model_kodu' => $row[0],
                'sezon' => $row[1],
                'siparis_numarasi' => $row[2],
                'ship_to' => $row[3],
                'tedarikci_termini' => $this->formatDate($row[4]),
                'renk_kodu_adi' => $row[5],
                'lot_kodu' => $row[6],
                'set_icerigi' => $row[7],
                'bedenler' => $sizes,
                'teslimat_ulkesi' => $row[$sizeColumnEnd + 1]
            ];
            
            $result[] = $orderData;
        }
        
        return $result;
    }
    
    /**
     * Tarihi formatla
     * 
     * @param string $date Tarih
     * @return string Formatlanmış tarih
     */
    private function formatDate($date) {
        // Tarih formatını kontrol et
        if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $date, $matches)) {
            // dd.mm.yyyy formatı
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        } elseif (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $date, $matches)) {
            // dd/mm/yyyy formatı
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }
        
        return $date;
    }
    
    /**
     * Önizleme HTML'i oluştur
     * 
     * @param array $data Ayrıştırılmış veriler
     * @return string HTML içeriği
     */
    private function generatePreview($data) {
        $html = '<div class="row">';
        
        // Toplam sipariş tablosu
        $html .= '<div class="col-md-12">';
        $html .= '<h4>Toplam Sipariş Tablosu</h4>';
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-bordered table-striped">';
        
        // Tablo başlıkları
        $html .= '<thead><tr>';
        $html .= '<th>Model Kodu</th>';
        $html .= '<th>Sezon</th>';
        $html .= '<th>Sipariş Numarası</th>';
        $html .= '<th>Ship To</th>';
        $html .= '<th>Tedarikçi Termini</th>';
        $html .= '<th>Renk Kodu-Adı</th>';
        $html .= '<th>Lot Kodu</th>';
        $html .= '<th>Set İçeriği</th>';
        
        // Beden başlıkları
        if (count($data['toplam_siparis']) > 0) {
            foreach ($data['toplam_siparis'][0]['bedenler'] as $size => $value) {
                $html .= '<th>' . $size . '</th>';
            }
        }
        
        $html .= '<th>Bir Lottaki Ürün Sayısı</th>';
        $html .= '<th>Teslimat Ülkesi</th>';
        $html .= '<th>Sipariş Geçilen Lot Sayısı</th>';
        $html .= '<th>Sipariş Geçilen Açık Adet Sayısı</th>';
        $html .= '<th>Depo Girişi Olan Lot Sayısı</th>';
        $html .= '<th>Depo Girişi Olan Açık Adet Sayısı</th>';
        $html .= '</tr></thead>';
        
        // Tablo içeriği
        $html .= '<tbody>';
        foreach ($data['toplam_siparis'] as $order) {
            $html .= '<tr>';
            $html .= '<td>' . $order['model_kodu'] . '</td>';
            $html .= '<td>' . $order['sezon'] . '</td>';
            $html .= '<td>' . $order['siparis_numarasi'] . '</td>';
            $html .= '<td>' . $order['ship_to'] . '</td>';
            $html .= '<td>' . $order['tedarikci_termini'] . '</td>';
            $html .= '<td>' . $order['renk_kodu_adi'] . '</td>';
            $html .= '<td>' . $order['lot_kodu'] . '</td>';
            $html .= '<td>' . $order['set_icerigi'] . '</td>';
            
            // Beden değerleri
            foreach ($order['bedenler'] as $size => $value) {
                $html .= '<td>' . ($value ?: '-') . '</td>';
            }
            
            $html .= '<td>' . $order['bir_lottaki_urun_sayisi'] . '</td>';
            $html .= '<td>' . $order['teslimat_ulkesi'] . '</td>';
            $html .= '<td>' . $order['siparis_gecilen_lot_sayisi'] . '</td>';
            $html .= '<td>' . $order['siparis_gecilen_acik_adet_sayisi'] . '</td>';
            $html .= '<td>' . $order['depo_girisi_olan_lot_sayisi'] . '</td>';
            $html .= '<td>' . $order['depo_girisi_olan_acik_adet_sayisi'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Beden bazlı toplam sipariş tablosu
        $html .= '<div class="col-md-12 margin-top-20">';
        $html .= '<h4>Beden Bazlı Toplam Sipariş Tablosu</h4>';
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-bordered table-striped">';
        
        // Tablo başlıkları
        $html .= '<thead><tr>';
        $html .= '<th>Model Kodu</th>';
        $html .= '<th>Sezon</th>';
        $html .= '<th>Sipariş Numarası</th>';
        $html .= '<th>Ship To</th>';
        $html .= '<th>Tedarikçi Termini</th>';
        $html .= '<th>Renk Kodu-Adı</th>';
        $html .= '<th>Lot Kodu</th>';
        $html .= '<th>Set İçeriği</th>';
        
        // Beden başlıkları
        if (count($data['beden_bazli']) > 0) {
            foreach ($data['beden_bazli'][0]['bedenler'] as $size => $value) {
                $html .= '<th>' . $size . '</th>';
            }
        }
        
        $html .= '<th>Teslimat Ülkesi</th>';
        $html .= '</tr></thead>';
        
        // Tablo içeriği
        $html .= '<tbody>';
        foreach ($data['beden_bazli'] as $order) {
            $html .= '<tr>';
            $html .= '<td>' . $order['model_kodu'] . '</td>';
            $html .= '<td>' . $order['sezon'] . '</td>';
            $html .= '<td>' . $order['siparis_numarasi'] . '</td>';
            $html .= '<td>' . $order['ship_to'] . '</td>';
            $html .= '<td>' . $order['tedarikci_termini'] . '</td>';
            $html .= '<td>' . $order['renk_kodu_adi'] . '</td>';
            $html .= '<td>' . $order['lot_kodu'] . '</td>';
            $html .= '<td>' . $order['set_icerigi'] . '</td>';
            
            // Beden değerleri
            foreach ($order['bedenler'] as $size => $value) {
                $html .= '<td>' . ($value ?: '-') . '</td>';
            }
            
            $html .= '<td>' . $order['teslimat_ulkesi'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Model listesi
     */
    public function listModels() {
        $modelModel = new ModelModel();
        $models = $modelModel->getAllWithDetails();
        
        $this->render('orders/list_models', [
            'pageTitle' => 'Model Listesi',
            'activePage' => 'orders-list',
            'models' => $models
        ]);
    }
    
    /**
     * Model detayları
     * 
     * @param string $model_kodu Model kodu
     */
    public function modelDetails($model_kodu) {
        $modelModel = new ModelModel();
        $model = $modelModel->getByModelKodu($model_kodu);
        
        if (!$model) {
            $this->redirect('/orders/list');
        }
        
        $orderModel = new OrderModel();
        $orders = $orderModel->getOrdersByModel($model_kodu);
        
        $boxModel = new BoxModel();
        $boxes = $boxModel->getBoxesByModelId($model['id']);
        
        $this->render('orders/model_details', [
            'pageTitle' => 'Model Detayları: ' . $model_kodu,
            'activePage' => 'orders-list',
            'model' => $model,
            'orders' => $orders,
            'boxes' => $boxes
        ]);
    }
    
    /**
     * Sipariş düzenleme formu
     * 
     * @param int $id Sipariş ID
     */
    public function edit($id) {
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderDetails($id);
        
        if (!$order) {
            $this->redirect('/orders/list');
        }
        
        $this->render('orders/edit', [
            'pageTitle' => 'Sipariş Düzenle',
            'activePage' => 'orders-list',
            'order' => $order
        ]);
    }
    
    /**
     * Sipariş güncelle
     */
    public function update() {
        $id = $this->post('id');
        $depo_girisi_olan_lot_sayisi = $this->post('depo_girisi_olan_lot_sayisi');
        $depo_girisi_olan_acik_adet_sayisi = $this->post('depo_girisi_olan_acik_adet_sayisi');
        
        $orderModel = new OrderModel();
        $order = $orderModel->getById($id);
        
        if (!$order) {
            $this->json([
                'success' => false,
                'error' => 'Sipariş bulunamadı'
            ]);
        }
        
        // Sipariş güncelle
        $result = $orderModel->update($id, [
            'depo_girisi_olan_lot_sayisi' => $depo_girisi_olan_lot_sayisi,
            'depo_girisi_olan_acik_adet_sayisi' => $depo_girisi_olan_acik_adet_sayisi
        ]);
        
        if ($result) {
            $this->json([
                'success' => true,
                'message' => 'Sipariş başarıyla güncellendi'
            ]);
        } else {
            $this->json([
                'success' => false,
                'error' => 'Sipariş güncellenirken hata oluştu'
            ]);
        }
    }
    
    /**
     * Sipariş sil
     */
    public function delete() {
        $id = $this->post('id');
        
        $orderModel = new OrderModel();
        $order = $orderModel->getById($id);
        
        if (!$order) {
            $this->json([
                'success' => false,
                'error' => 'Sipariş bulunamadı'
            ]);
        }
        
        // Sipariş sil
        $result = $orderModel->delete($id);
        
        if ($result) {
            $this->json([
                'success' => true,
                'message' => 'Sipariş başarıyla silindi'
            ]);
        } else {
            $this->json([
                'success' => false,
                'error' => 'Sipariş silinirken hata oluştu'
            ]);
        }
    }
}