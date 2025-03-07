<?php
/**
 * Rapor Controller
 */
class ReportController extends Controller {
    /**
     * Rapor sayfası
     */
    public function index() {
        // Rapor tipini al
        $reportType = $this->get('type', 'general');
        
        // Filtreleme parametrelerini al
        $filters = $this->getFilters();
        
        // Rapor verilerini al
        $reportData = $this->generateReport($reportType, $filters);
        
        $this->render('reports/index', [
            'pageTitle' => 'Raporlar',
            'activePage' => 'reports',
            'reportType' => $reportType,
            'filters' => $filters,
            'data' => $reportData
        ]);
    }
    
    /**
     * Rapor verilerini dışa aktar
     */
    public function export() {
        // Rapor tipini al
        $reportType = $this->get('type', 'general');
        
        // Dışa aktarma formatını al
        $format = $this->get('format', 'excel');
        
        // Filtreleme parametrelerini al
        $filters = $this->getFilters();
        
        // Rapor verilerini al
        $reportData = $this->generateReport($reportType, $filters);
        
        // Dışa aktarma formatına göre işlem yap
        switch ($format) {
            case 'excel':
                $this->exportExcel($reportType, $reportData, $filters);
                break;
            case 'pdf':
                $this->exportPdf($reportType, $reportData, $filters);
                break;
            case 'csv':
                $this->exportCsv($reportType, $reportData, $filters);
                break;
            default:
                $this->redirect('/reports?type=' . $reportType);
        }
    }
    
    /**
     * Filtreleri al
     * 
     * @return array Filtreler
     */
    private function getFilters() {
        return [
            'model_kodu' => $this->get('model_kodu'),
            'siparis_numarasi' => $this->get('siparis_numarasi'),
            'teslimat_ulkesi' => $this->get('teslimat_ulkesi'),
            'tarih_baslangic' => $this->get('tarih_baslangic'),
            'tarih_bitis' => $this->get('tarih_bitis'),
            'durum' => $this->get('durum')
        ];
    }
    
    /**
     * Rapor verilerini oluştur
     * 
     * @param string $reportType Rapor tipi
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    private function generateReport($reportType, $filters) {
        $result = [];
        
        switch ($reportType) {
            case 'model':
                $result = $this->generateModelReport($filters);
                break;
            case 'order':
                $result = $this->generateOrderReport($filters);
                break;
            case 'box':
                $result = $this->generateBoxReport($filters);
                break;
            case 'label':
                $result = $this->generateLabelReport($filters);
                break;
            default:
                $result = $this->generateGeneralReport($filters);
        }
        
        return $result;
    }
    
    /**
     * Genel rapor oluştur
     * 
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    private function generateGeneralReport($filters) {
        // Model istatistiklerini al
        $modelModel = new ModelModel();
        $modelStats = $modelModel->getStatistics();
        
        // Sipariş istatistiklerini al
        $orderModel = new OrderModel();
        $orderStats = $orderModel->getStatistics();
        
        // Koli istatistiklerini al
        $boxModel = new BoxModel();
        $boxStats = $boxModel->getStatistics();
        
        return [
            'model_stats' => $modelStats,
            'order_stats' => $orderStats,
            'box_stats' => $boxStats
        ];
    }
    
    /**
     * Model raporu oluştur
     * 
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    private function generateModelReport($filters) {
        $modelModel = new ModelModel();
        
        // SQL sorgusu oluştur
        $sql = "SELECT m.*, 
               (SELECT COUNT(*) FROM orders o WHERE o.model_id = m.id) as order_count,
               (SELECT COUNT(*) FROM boxes b WHERE b.model_id = m.id) as box_count,
               (SELECT SUM(o.siparis_gecilen_lot_sayisi) FROM orders o WHERE o.model_id = m.id) as total_lots,
               (SELECT SUM(o.depo_girisi_olan_lot_sayisi) FROM orders o WHERE o.model_id = m.id) as total_deposited_lots
               FROM models m";
        
        $conditions = [];
        $params = [];
        
        // Model kodu filtresi
        if (!empty($filters['model_kodu'])) {
            $conditions[] = "m.model_kodu LIKE :model_kodu";
            $params['model_kodu'] = '%' . $filters['model_kodu'] . '%';
        }
        
        // Tarih filtresi
        if (!empty($filters['tarih_baslangic'])) {
            $conditions[] = "m.created_at >= :tarih_baslangic";
            $params['tarih_baslangic'] = $filters['tarih_baslangic'] . ' 00:00:00';
        }
        
        if (!empty($filters['tarih_bitis'])) {
            $conditions[] = "m.created_at <= :tarih_bitis";
            $params['tarih_bitis'] = $filters['tarih_bitis'] . ' 23:59:59';
        }
        
        // WHERE koşulunu ekle
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY m.model_kodu";
        
        return $modelModel->query($sql, $params);
    }
    
    /**
     * Sipariş raporu oluştur
     * 
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    private function generateOrderReport($filters) {
        $orderModel = new OrderModel();
        
        // SQL sorgusu oluştur
        $sql = "SELECT o.*, m.model_kodu,
               (o.siparis_gecilen_lot_sayisi - o.depo_girisi_olan_lot_sayisi) as remaining_lots,
               (o.siparis_gecilen_lot_sayisi = o.depo_girisi_olan_lot_sayisi) as is_completed
               FROM orders o
               JOIN models m ON o.model_id = m.id";
        
        $conditions = [];
        $params = [];
        
        // Model kodu filtresi
        if (!empty($filters['model_kodu'])) {
            $conditions[] = "m.model_kodu LIKE :model_kodu";
            $params['model_kodu'] = '%' . $filters['model_kodu'] . '%';
        }
        
        // Sipariş numarası filtresi
        if (!empty($filters['siparis_numarasi'])) {
            $conditions[] = "o.siparis_numarasi LIKE :siparis_numarasi";
            $params['siparis_numarasi'] = '%' . $filters['siparis_numarasi'] . '%';
        }
        
        // Teslimat ülkesi filtresi
        if (!empty($filters['teslimat_ulkesi'])) {
            $conditions[] = "o.teslimat_ulkesi = :teslimat_ulkesi";
            $params['teslimat_ulkesi'] = $filters['teslimat_ulkesi'];
        }
        
        // Tarih filtresi
        if (!empty($filters['tarih_baslangic'])) {
            $conditions[] = "o.created_at >= :tarih_baslangic";
            $params['tarih_baslangic'] = $filters['tarih_baslangic'] . ' 00:00:00';
        }
        
        if (!empty($filters['tarih_bitis'])) {
            $conditions[] = "o.created_at <= :tarih_bitis";
            $params['tarih_bitis'] = $filters['tarih_bitis'] . ' 23:59:59';
        }
        
        // Durum filtresi
        if (!empty($filters['durum'])) {
            if ($filters['durum'] === 'completed') {
                $conditions[] = "o.siparis_gecilen_lot_sayisi = o.depo_girisi_olan_lot_sayisi";
            } elseif ($filters['durum'] === 'pending') {
                $conditions[] = "o.siparis_gecilen_lot_sayisi > o.depo_girisi_olan_lot_sayisi";
            }
        }
        
        // WHERE koşulunu ekle
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY m.model_kodu, o.siparis_numarasi";
        
        return $orderModel->query($sql, $params);
    }
    
    /**
     * Koli raporu oluştur
     * 
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    private function generateBoxReport($filters) {
        $boxModel = new BoxModel();
        
        // SQL sorgusu oluştur
        $sql = "SELECT b.*, m.model_kodu, o.siparis_numarasi, o.teslimat_ulkesi, o.lot_kodu,
               (SELECT label_status FROM box_labels bl WHERE bl.box_id = b.id ORDER BY bl.created_at DESC LIMIT 1) as label_status
               FROM boxes b
               JOIN models m ON b.model_id = m.id
               JOIN orders o ON b.siparis_id = o.id";
        
        $conditions = [];
        $params = [];
        
        // Model kodu filtresi
        if (!empty($filters['model_kodu'])) {
            $conditions[] = "m.model_kodu LIKE :model_kodu";
            $params['model_kodu'] = '%' . $filters['model_kodu'] . '%';
        }
        
        // Sipariş numarası filtresi
        if (!empty($filters['siparis_numarasi'])) {
            $conditions[] = "o.siparis_numarasi LIKE :siparis_numarasi";
            $params['siparis_numarasi'] = '%' . $filters['siparis_numarasi'] . '%';
        }
        
        // Teslimat ülkesi filtresi
        if (!empty($filters['teslimat_ulkesi'])) {
            $conditions[] = "o.teslimat_ulkesi = :teslimat_ulkesi";
            $params['teslimat_ulkesi'] = $filters['teslimat_ulkesi'];
        }
        
        // Tarih filtresi
        if (!empty($filters['tarih_baslangic'])) {
            $conditions[] = "b.created_at >= :tarih_baslangic";
            $params['tarih_baslangic'] = $filters['tarih_baslangic'] . ' 00:00:00';
        }
        
        if (!empty($filters['tarih_bitis'])) {
            $conditions[] = "b.created_at <= :tarih_bitis";
            $params['tarih_bitis'] = $filters['tarih_bitis'] . ' 23:59:59';
        }
        
        // Durum filtresi
        if (!empty($filters['durum'])) {
            $conditions[] = "b.status = :durum";
            $params['durum'] = $filters['durum'];
        }
        
        // WHERE koşulunu ekle
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY m.model_kodu, o.siparis_numarasi, b.box_number";
        
        return $boxModel->query($sql, $params);
    }
    
    /**
     * Etiket raporu oluştur
     * 
     * @param array $filters Filtreler
     * @return array Rapor verileri
     */
    private function generateLabelReport($filters) {
        $db = App::getInstance()->getDB();
        
        // SQL sorgusu oluştur
        $sql = "SELECT bl.*, b.box_type, b.lot_count, b.box_number, b.status as box_status,
               m.model_kodu, o.siparis_numarasi, o.teslimat_ulkesi, o.lot_kodu
               FROM box_labels bl
               JOIN boxes b ON bl.box_id = b.id
               JOIN models m ON b.model_id = m.id
               JOIN orders o ON b.siparis_id = o.id";
        
        $conditions = [];
        $params = [];
        
        // Model kodu filtresi
        if (!empty($filters['model_kodu'])) {
            $conditions[] = "m.model_kodu LIKE :model_kodu";
            $params['model_kodu'] = '%' . $filters['model_kodu'] . '%';
        }
        
        // Sipariş numarası filtresi
        if (!empty($filters['siparis_numarasi'])) {
            $conditions[] = "o.siparis_numarasi LIKE :siparis_numarasi";
            $params['siparis_numarasi'] = '%' . $filters['siparis_numarasi'] . '%';
        }
        
        // Teslimat ülkesi filtresi
        if (!empty($filters['teslimat_ulkesi'])) {
            $conditions[] = "o.teslimat_ulkesi = :teslimat_ulkesi";
            $params['teslimat_ulkesi'] = $filters['teslimat_ulkesi'];
        }
        
        // Tarih filtresi
        if (!empty($filters['tarih_baslangic'])) {
            $conditions[] = "bl.created_at >= :tarih_baslangic";
            $params['tarih_baslangic'] = $filters['tarih_baslangic'] . ' 00:00:00';
        }
        
        if (!empty($filters['tarih_bitis'])) {
            $conditions[] = "bl.created_at <= :tarih_bitis";
            $params['tarih_bitis'] = $filters['tarih_bitis'] . ' 23:59:59';
        }
        
        // Durum filtresi
        if (!empty($filters['durum'])) {
            $conditions[] = "bl.label_status = :durum";
            $params['durum'] = $filters['durum'];
        }
        
        // WHERE koşulunu ekle
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY m.model_kodu, o.siparis_numarasi, b.box_number, bl.created_at DESC";
        
        $db->query($sql);
        
        foreach ($params as $key => $value) {
            $db->bind(':' . $key, $value);
        }
        
        return $db->fetchAll();
    }
    
    /**
     * Excel olarak dışa aktar
     * 
     * @param string $reportType Rapor tipi
     * @param array $data Rapor verileri
     * @param array $filters Filtreler
     * @return void
     */
    private function exportExcel($reportType, $data, $filters) {
        // Excel dosyası oluştur
        // Not: Bu örnekte gerçek bir Excel dosyası oluşturmuyoruz, sadece CSV formatında indiriyoruz
        
        // Dosya adını belirle
        $filename = 'rapor_' . $reportType . '_' . date('Ymd_His') . '.csv';
        
        // HTTP başlıklarını ayarla
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Çıktı tamponunu aç
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM ekle
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Başlık satırını yaz
        $headers = $this->getReportHeaders($reportType);
        fputcsv($output, $headers, ';');
        
        // Veri satırlarını yaz
        foreach ($this->getReportRows($reportType, $data) as $row) {
            fputcsv($output, $row, ';');
        }
        
        // Çıktı tamponunu kapat
        fclose($output);
        exit;
    }
    
    /**
     * PDF olarak dışa aktar
     * 
     * @param string $reportType Rapor tipi
     * @param array $data Rapor verileri
     * @param array $filters Filtreler
     * @return void
     */
    private function exportPdf($reportType, $data, $filters) {
        // PDF dosyası oluştur
        // Not: Bu örnekte gerçek bir PDF dosyası oluşturmuyoruz, sadece HTML olarak gösteriyoruz
        
        // Rapor başlığını belirle
        $title = $this->getReportTitle($reportType);
        
        // HTML içeriğini oluştur
        $html = '<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>' . $title . '</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #ddd; padding: 8px; }
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>
                    <h1>' . $title . '</h1>
                    <table>
                        <thead>
                            <tr>';
        
        // Başlık satırını ekle
        $headers = $this->getReportHeaders($reportType);
        foreach ($headers as $header) {
            $html .= '<th>' . $header . '</th>';
        }
        
        $html .= '</tr>
                </thead>
                <tbody>';
        
        // Veri satırlarını ekle
        foreach ($this->getReportRows($reportType, $data) as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . $cell . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody>
                </table>
            </body>
            </html>';
        
        // HTTP başlıklarını ayarla
        header('Content-Type: text/html; charset=utf-8');
        
        // HTML içeriğini göster
        echo $html;
        exit;
    }
    
    /**
     * CSV olarak dışa aktar
     * 
     * @param string $reportType Rapor tipi
     * @param array $data Rapor verileri
     * @param array $filters Filtreler
     * @return void
     */
    private function exportCsv($reportType, $data, $filters) {
        // Dosya adını belirle
        $filename = 'rapor_' . $reportType . '_' . date('Ymd_His') . '.csv';
        
        // HTTP başlıklarını ayarla
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Çıktı tamponunu aç
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM ekle
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Başlık satırını yaz
        $headers = $this->getReportHeaders($reportType);
        fputcsv($output, $headers, ';');
        
        // Veri satırlarını yaz
        foreach ($this->getReportRows($reportType, $data) as $row) {
            fputcsv($output, $row, ';');
        }
        
        // Çıktı tamponunu kapat
        fclose($output);
        exit;
    }
    
    /**
     * Rapor başlığını al
     * 
     * @param string $reportType Rapor tipi
     * @return string Rapor başlığı
     */
    private function getReportTitle($reportType) {
        switch ($reportType) {
            case 'model':
                return 'Model Raporu';
            case 'order':
                return 'Sipariş Raporu';
            case 'box':
                return 'Koli Raporu';
            case 'label':
                return 'Etiket Raporu';
            default:
                return 'Genel Rapor';
        }
    }
    
    /**
     * Rapor başlıklarını al
     * 
     * @param string $reportType Rapor tipi
     * @return array Rapor başlıkları
     */
    private function getReportHeaders($reportType) {
        switch ($reportType) {
            case 'model':
                return [
                    'Model Kodu',
                    'Resim',
                    'Sipariş Sayısı',
                    'Koli Sayısı',
                    'Toplam Lot',
                    'Depo Girişi Olan Lot',
                    'Oluşturulma Tarihi',
                    'Güncelleme Tarihi'
                ];
            case 'order':
                return [
                    'Model Kodu',
                    'Sezon',
                    'Sipariş Numarası',
                    'Ship To',
                    'Tedarikçi Termini',
                    'Renk Kodu-Adı',
                    'Lot Kodu',
                    'Teslimat Ülkesi',
                    'Sipariş Lot',
                    'Depo Girişi Lot',
                    'Kalan Lot',
                    'Durum',
                    'Oluşturulma Tarihi'
                ];
            case 'box':
                return [
                    'Model Kodu',
                    'Sipariş Numarası',
                    'Teslimat Ülkesi',
                    'Lot Kodu',
                    'Koli Tipi',
                    'Koli Numarası',
                    'Lot Sayısı',
                    'Durum',
                    'Etiket Durumu',
                    'Oluşturulma Tarihi'
                ];
            case 'label':
                return [
                    'Model Kodu',
                    'Sipariş Numarası',
                    'Teslimat Ülkesi',
                    'Lot Kodu',
                    'Koli Tipi',
                    'Koli Numarası',
                    'Etiket Durumu',
                    'Teslim Edilen Kişi',
                    'Teslim Tarihi',
                    'Teslim Adet',
                    'Notlar',
                    'Oluşturulma Tarihi'
                ];
            default:
                return [
                    'Model Sayısı',
                    'Sipariş Sayısı',
                    'Koli Sayısı',
                    'Toplam Lot',
                    'Depo Girişi Olan Lot',
                    'Tamamlanan Sipariş'
                ];
        }
    }
    
    /**
     * Rapor satırlarını al
     * 
     * @param string $reportType Rapor tipi
     * @param array $data Rapor verileri
     * @return array Rapor satırları
     */
    private function getReportRows($reportType, $data) {
        $rows = [];
        
        switch ($reportType) {
            case 'model':
                foreach ($data as $model) {
                    $rows[] = [
                        $model['model_kodu'],
                        $model['image_path'] ? 'Var' : 'Yok',
                        $model['order_count'],
                        $model['box_count'],
                        $model['total_lots'] ?: 0,
                        $model['total_deposited_lots'] ?: 0,
                        format_date($model['created_at'], DATETIME_FORMAT),
                        format_date($model['updated_at'], DATETIME_FORMAT)
                    ];
                }
                break;
            case 'order':
                foreach ($data as $order) {
                    $rows[] = [
                        $order['model_kodu'],
                        $order['sezon'],
                        $order['siparis_numarasi'],
                        $order['ship_to'],
                        format_date($order['tedarikci_termini']),
                        $order['renk_kodu_adi'],
                        $order['lot_kodu'],
                        $order['teslimat_ulkesi'],
                        $order['siparis_gecilen_lot_sayisi'],
                        $order['depo_girisi_olan_lot_sayisi'],
                        $order['remaining_lots'],
                        $order['is_completed'] ? 'Tamamlandı' : 'Devam Ediyor',
                        format_date($order['created_at'], DATETIME_FORMAT)
                    ];
                }
                break;
            case 'box':
                foreach ($data as $box) {
                    $rows[] = [
                        $box['model_kodu'],
                        $box['siparis_numarasi'],
                        $box['teslimat_ulkesi'],
                        $box['lot_kodu'],
                        $box['box_type'] === 'tam' ? 'Tam Koli' : 'Kırık Koli',
                        $box['box_number'],
                        $box['lot_count'],
                        $this->getBoxStatusText($box['status']),
                        $this->getLabelStatusText($box['label_status']),
                        format_date($box['created_at'], DATETIME_FORMAT)
                    ];
                }
                break;
            case 'label':
                foreach ($data as $label) {
                    $rows[] = [
                        $label['model_kodu'],
                        $label['siparis_numarasi'],
                        $label['teslimat_ulkesi'],
                        $label['lot_kodu'],
                        $label['box_type'] === 'tam' ? 'Tam Koli' : 'Kırık Koli',
                        $label['box_number'],
                        $this->getLabelStatusText($label['label_status']),
                        $label['teslim_edilen_kisi'] ?: '-',
                        $label['teslim_tarihi'] ? format_date($label['teslim_tarihi'], DATETIME_FORMAT) : '-',
                        $label['teslim_adet'] ?: '-',
                        $label['notes'] ?: '-',
                        format_date($label['created_at'], DATETIME_FORMAT)
                    ];
                }
                break;
            default:
                $rows[] = [
                    $data['model_stats']['total'],
                    $data['order_stats']['total_orders'],
                    $data['box_stats']['total_boxes'],
                    $data['order_stats']['total_lots'],
                    $data['order_stats']['total_deposited_lots'],
                    $data['order_stats']['completed_orders']
                ];
        }
        
        return $rows;
    }
    
    /**
     * Koli durumu metnini al
     * 
     * @param string $status Durum
     * @return string Durum metni
     */
    private function getBoxStatusText($status) {
        switch ($status) {
            case 'hazırlanıyor':
                return 'Hazırlanıyor';
            case 'hazır':
                return 'Hazır';
            case 'etiket_basıldı':
                return 'Etiket Basıldı';
            case 'teslim_edildi':
                return 'Teslim Edildi';
            default:
                return $status;
        }
    }
    
    /**
     * Etiket durumu metnini al
     * 
     * @param string $status Durum
     * @return string Durum metni
     */
    private function getLabelStatusText($status) {
        switch ($status) {
            case 'indirilmedi':
                return 'İndirilmedi';
            case 'indirildi':
                return 'İndirildi';
            case 'basıldı':
                return 'Basıldı';
            case 'kayıp':
                return 'Kayıp';
            case 'tekrar_basıldı':
                return 'Tekrar Basıldı';
            default:
                return $status ?: 'Belirsiz';
        }
    }
}