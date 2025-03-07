<?php
/**
 * Koli Controller
 */
class BoxController extends Controller {
    /**
     * Koli hesaplama formu
     */
    public function calculateForm() {
        $this->render('boxes/calculate', [
            'pageTitle' => 'Koli Hesaplama',
            'activePage' => 'boxes-calculate'
        ]);
    }
    
    /**
     * Koli hesaplamalarını işle
     */
    public function processCalculation() {
        // POST verilerini al
        $modelKodu = $this->post('model_kodu');
        $siparisNumarasi = $this->post('siparis_numarasi');
        $koliLotSayisi = $this->post('koli_lot_sayisi', 10);
        $hesaplamaTipi = $this->post('hesaplama_tipi', 'genel');
        $saveCalculation = $this->post('save_calculation', false);
        
        if (!$modelKodu) {
            $this->json([
                'success' => false,
                'error' => 'Model kodu gereklidir'
            ]);
        }
        
        // Model koduna göre siparişleri getir
        $modelModel = new ModelModel();
        $model = $modelModel->getByModelKodu($modelKodu);
        
        if (!$model) {
            $this->json([
                'success' => false,
                'error' => 'Model bulunamadı'
            ]);
        }
        
        $orderModel = new OrderModel();
        $orders = $orderModel->getOrdersByModel($modelKodu, $siparisNumarasi);
        
        if (empty($orders)) {
            $this->json([
                'success' => false,
                'error' => 'Sipariş bulunamadı'
            ]);
        }
        
        $boxModel = new BoxModel();
        $results = [];
        $boxIds = [];
        
        foreach ($orders as $order) {
            // Depo girişi kontrolü
            $remainingLots = $order['siparis_gecilen_lot_sayisi'] - $order['depo_girisi_olan_lot_sayisi'];
            
            // Koli hesaplaması
            $boxCalculation = $boxModel->calculateBoxes($order, $koliLotSayisi, $remainingLots);
            
            // Hesaplamaları kaydet
            if ($saveCalculation && $boxCalculation['kalan_lot'] > 0) {
                $newBoxIds = $boxModel->saveCalculation($boxCalculation, $order['id'], $model['id']);
                $boxIds = array_merge($boxIds, $newBoxIds);
            }
            
            $results[] = [
                'order' => $order,
                'calculation' => $boxCalculation,
                'completed' => ($remainingLots <= 0)
            ];
        }
        
        $this->json([
            'success' => true,
            'data' => $results,
            'saved' => $saveCalculation,
            'box_ids' => $boxIds
        ]);
    }
    
    /**
     * Koli listesi
     */
    public function listBoxes() {
        // Filtreleme parametrelerini al
        $modelKodu = $this->get('model_kodu');
        $siparisNumarasi = $this->get('siparis_numarasi');
        $status = $this->get('status');
        
        // Model listesini al
        $modelModel = new ModelModel();
        $models = $modelModel->getAll();
        
        // Filtreler varsa kolileri getir
        $boxes = [];
        if ($modelKodu || $siparisNumarasi || $status) {
            $boxModel = new BoxModel();
            
            $conditions = [];
            $params = [];
            
            if ($modelKodu) {
                $model = $modelModel->getByModelKodu($modelKodu);
                if ($model) {
                    $conditions[] = 'b.model_id = :model_id';
                    $params['model_id'] = $model['id'];
                }
            }
            
            if ($siparisNumarasi) {
                $conditions[] = 'o.siparis_numarasi = :siparis_numarasi';
                $params['siparis_numarasi'] = $siparisNumarasi;
            }
            
            if ($status) {
                $conditions[] = 'b.status = :status';
                $params['status'] = $status;
            }
            
            $whereClause = '';
            if (!empty($conditions)) {
                $whereClause = 'WHERE ' . implode(' AND ', $conditions);
            }
            
            $sql = "SELECT b.*, 
                   m.model_kodu, 
                   o.siparis_numarasi, o.teslimat_ulkesi, o.lot_kodu,
                   (SELECT label_status FROM box_labels bl WHERE bl.box_id = b.id ORDER BY bl.created_at DESC LIMIT 1) as label_status 
                   FROM boxes b 
                   JOIN models m ON b.model_id = m.id
                   JOIN orders o ON b.siparis_id = o.id
                   $whereClause
                   ORDER BY m.model_kodu, o.siparis_numarasi, b.box_number";
            
            $boxes = $boxModel->query($sql, $params);
        }
        
        $this->render('boxes/list', [
            'pageTitle' => 'Koli Listesi',
            'activePage' => 'boxes-list',
            'models' => $models,
            'boxes' => $boxes,
            'filters' => [
                'model_kodu' => $modelKodu,
                'siparis_numarasi' => $siparisNumarasi,
                'status' => $status
            ]
        ]);
    }
    
    /**
     * Koli detayları
     * 
     * @param int $id Koli ID
     */
    public function viewBox($id) {
        $boxModel = new BoxModel();
        $box = $boxModel->getById($id);
        
        if (!$box) {
            $this->redirect('/boxes/list');
        }
        
        // Model bilgilerini al
        $modelModel = new ModelModel();
        $model = $modelModel->getById($box['model_id']);
        
        // Sipariş bilgilerini al
        $orderModel = new OrderModel();
        $order = $orderModel->getById($box['siparis_id']);
        
        // Beden bilgilerini al
        $orderSizeModel = new OrderSizeModel();
        $sizes = $orderSizeModel->getByOrderId($box['siparis_id']);
        
        // Etiket geçmişini al
        $this->db = App::getInstance()->getDB();
        $this->db->query("SELECT * FROM box_labels WHERE box_id = :box_id ORDER BY created_at DESC");
        $this->db->bind(':box_id', $id);
        $labels = $this->db->fetchAll();
        
        $this->render('boxes/view', [
            'pageTitle' => 'Koli Detayları',
            'activePage' => 'boxes-list',
            'box' => $box,
            'model' => $model,
            'order' => $order,
            'sizes' => $sizes,
            'labels' => $labels
        ]);
    }
    
    /**
     * Koli durumunu güncelle
     */
    public function updateStatus() {
        $boxId = $this->post('box_id');
        $status = $this->post('status');
        
        $boxModel = new BoxModel();
        $box = $boxModel->getById($boxId);
        
        if (!$box) {
            $this->json([
                'success' => false,
                'error' => 'Koli bulunamadı'
            ]);
        }
        
        // Koli durumunu güncelle
        $result = $boxModel->updateStatus($boxId, $status);
        
        if ($result) {
            $this->json([
                'success' => true,
                'message' => 'Koli durumu başarıyla güncellendi'
            ]);
        } else {
            $this->json([
                'success' => false,
                'error' => 'Koli durumu güncellenirken hata oluştu'
            ]);
        }
    }
    
    /**
     * Koli etiket durumunu güncelle
     */
    public function updateLabelStatus() {
        $boxId = $this->post('box_id');
        $labelStatus = $this->post('label_status');
        $teslimEdilenKisi = $this->post('teslim_edilen_kisi');
        $teslimTarihi = $this->post('teslim_tarihi');
        $teslimAdet = $this->post('teslim_adet');
        $notes = $this->post('notes');
        
        $boxModel = new BoxModel();
        $box = $boxModel->getById($boxId);
        
        if (!$box) {
            $this->json([
                'success' => false,
                'error' => 'Koli bulunamadı'
            ]);
        }
        
        // Koli etiket durumunu güncelle
        $result = $boxModel->updateLabelStatus($boxId, $labelStatus, [
            'teslim_edilen_kisi' => $teslimEdilenKisi,
            'teslim_tarihi' => $teslimTarihi,
            'teslim_adet' => $teslimAdet,
            'notes' => $notes
        ]);
        
        if ($result) {
            $this->json([
                'success' => true,
                'message' => 'Koli etiket durumu başarıyla güncellendi'
            ]);
        } else {
            $this->json([
                'success' => false,
                'error' => 'Koli etiket durumu güncellenirken hata oluştu'
            ]);
        }
    }
}