<div class="row">
    <div class="col-md-12">
        <!-- Model Bilgileri -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Model Detayları</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                </div>
                <div class="actions">
                    <a href="<?= url('/orders/list') ?>" class="btn btn-circle btn-default">
                        <i class="fa fa-arrow-left"></i> Geri
                    </a>
                    <a href="<?= url('/boxes/calculate?model_kodu=' . $model['model_kodu']) ?>" class="btn btn-circle btn-primary">
                        <i class="fa fa-calculator"></i> Koli Hesapla
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <?php if ($model['image_path']): ?>
                                <img src="<?= url($model['image_path']) ?>" alt="<?= $model['model_kodu'] ?>" class="img-responsive center-block" style="max-height: 200px;">
                            <?php else: ?>
                                <div class="thumbnail" style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #f5f5f5;">
                                    <span class="font-grey-silver">
                                        <i class="fa fa-image fa-5x"></i><br>
                                        Resim Yok
                                    </span>
                                </div>
                            <?php endif; ?>
                            <button type="button" class="btn btn-sm green" onclick="uploadModelImage(<?= $model['id'] ?>, '<?= $model['model_kodu'] ?>')">
                                <i class="fa fa-upload"></i> Resim Ekle/Değiştir
                            </button>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <h3><?= $model['model_kodu'] ?></h3>
                        <div class="row static-info">
                            <div class="col-md-5 name">Sipariş Sayısı:</div>
                            <div class="col-md-7 value"><?= count($orders) ?></div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name">Koli Sayısı:</div>
                            <div class="col-md-7 value"><?= count($boxes) ?></div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name">Oluşturulma Tarihi:</div>
                            <div class="col-md-7 value"><?= format_date($model['created_at'], DATETIME_FORMAT) ?></div>
                        </div>
                        <div class="row static-info">
                            <div class="col-md-5 name">Son Güncelleme:</div>
                            <div class="col-md-7 value"><?= format_date($model['updated_at'], DATETIME_FORMAT) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Siparişler -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-shopping-cart font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Siparişler</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="orderAccordion" class="panel-group accordion">
                    <?php foreach ($orders as $index => $order): ?>
                        <?php 
                        // Sipariş durumu
                        $completed = ($order['siparis_gecilen_lot_sayisi'] <= $order['depo_girisi_olan_lot_sayisi']);
                        $statusClass = $completed ? 'success' : 'warning';
                        $statusText = $completed ? 'TESLİMAT TAMAMLANMIŞ' : 'DEVAM EDİYOR';
                        
                        // Beden verileri
                        $bedenler = [];
                        foreach ($order['bedenler'] as $beden) {
                            $bedenler[$beden['beden_adi']] = $beden['adet'];
                        }
                        ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#orderAccordion" href="#collapse<?= $index ?>">
                                        <span class="label label-<?= $statusClass ?>"><?= $statusText ?></span>
                                        Sipariş No: <?= $order['siparis_numarasi'] ?> - 
                                        Lot: <?= $order['lot_kodu'] ?> - 
                                        Ülke: <?= $order['teslimat_ulkesi'] ?> - 
                                        Lot Sayısı: <?= $order['siparis_gecilen_lot_sayisi'] ?> / <?= $order['depo_girisi_olan_lot_sayisi'] ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse<?= $index ?>" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4>Sipariş Detayları</h4>
                                            <table class="table table-striped table-bordered">
                                                <tr>
                                                    <th>Sezon</th>
                                                    <td><?= $order['sezon'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Ship To</th>
                                                    <td><?= $order['ship_to'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Tedarikçi Termini</th>
                                                    <td><?= format_date($order['tedarikci_termini']) ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Renk Kodu-Adı</th>
                                                    <td><?= $order['renk_kodu_adi'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Set İçeriği</th>
                                                    <td><?= $order['set_icerigi'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Bir Lottaki Ürün Sayısı</th>
                                                    <td><?= $order['bir_lottaki_urun_sayisi'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Sipariş Geçilen Lot Sayısı</th>
                                                    <td><?= $order['siparis_gecilen_lot_sayisi'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Sipariş Geçilen Açık Adet Sayısı</th>
                                                    <td><?= $order['siparis_gecilen_acik_adet_sayisi'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th>Depo Girişi Olan Lot Sayısı</th>
                                                    <td>
                                                        <span class="editable" 
                                                              data-pk="<?= $order['id'] ?>" 
                                                              data-name="depo_girisi_olan_lot_sayisi" 
                                                              data-url="<?= url('/orders/update') ?>" 
                                                              data-type="number" 
                                                              data-min="0" 
                                                              data-max="<?= $order['siparis_gecilen_lot_sayisi'] ?>">
                                                            <?= $order['depo_girisi_olan_lot_sayisi'] ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Depo Girişi Olan Açık Adet Sayısı</th>
                                                    <td>
                                                        <span class="editable" 
                                                              data-pk="<?= $order['id'] ?>" 
                                                              data-name="depo_girisi_olan_acik_adet_sayisi" 
                                                              data-url="<?= url('/orders/update') ?>" 
                                                              data-type="number" 
                                                              data-min="0" 
                                                              data-max="<?= $order['siparis_gecilen_acik_adet_sayisi'] ?>">
                                                            <?= $order['depo_girisi_olan_acik_adet_sayisi'] ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Beden Dağılımı</h4>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Beden</th>
                                                        <th>Adet</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($order['bedenler'] as $beden): ?>
                                                        <tr>
                                                            <td><?= $beden['beden_adi'] ?></td>
                                                            <td><?= $beden['adet'] ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <!-- Koliler -->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cube font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Koliler</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
            </div>
            <div class="portlet-body">
                <?php if (empty($boxes)): ?>
                    <div class="alert alert-info">
                        <strong>Bilgi:</strong> Bu modele ait henüz koli hesaplaması yapılmamış.
                        <a href="<?= url('/boxes/calculate?model_kodu=' . $model['model_kodu']) ?>" class="btn btn-sm blue">
                            <i class="fa fa-calculator"></i> Koli Hesapla
                        </a>
                    </div>
                <?php else: ?>
                    <div id="boxGrid"></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Sayfa özel script
$pageScript = '
    // X-Editable
    $.fn.editable.defaults.mode = "inline";
    $(".editable").editable({
        success: function(response, newValue) {
            if (response && response.success) {
                // Başarılı
            } else {
                return response && response.error ? response.error : "Güncelleme başarısız oldu.";
            }
        }
    });
    
    // Koli Grid
    var boxData = ' . (!empty($boxes) ? json_encode($boxes) : '[]') . ';
    
    $("#boxGrid").kendoGrid({
        dataSource: {
            data: boxData,
            schema: {
                model: {
                    fields: {
                        id: { type: "number" },
                        siparis_numarasi: { type: "string" },
                        teslimat_ulkesi: { type: "string" },
                        box_type: { type: "string" },
                        box_number: { type: "number" },
                        lot_count: { type: "number" },
                        status: { type: "string" },
                        label_status: { type: "string" },
                        created_at: { type: "date" }
                    }
                }
            },
            pageSize: 10
        },
        height: 400,
        sortable: true,
        filterable: true,
        pageable: {
            refresh: true,
            pageSizes: [5, 10, 20, 50],
            buttonCount: 5
        },
        columns: [
            { 
                field: "siparis_numarasi", 
                title: "Sipariş Numarası",
                width: 150
            },
            { 
                field: "teslimat_ulkesi", 
                title: "Teslimat Ülkesi",
                width: 120
            },
            { 
                field: "box_type", 
                title: "Koli Tipi",
                width: 100,
                template: function(dataItem) {
                    if (dataItem.box_type === "tam") {
                        return "<span class=\'label label-success\'>Tam Koli</span>";
                    } else {
                        return "<span class=\'label label-warning\'>Kırık Koli</span>";
                    }
                }
            },
            { 
                field: "box_number", 
                title: "Koli No",
                width: 80
            },
            { 
                field: "lot_count", 
                title: "Lot Sayısı",
                width: 100
            },
            { 
                field: "status", 
                title: "Durum",
                width: 120,
                template: function(dataItem) {
                    var statusClass = "";
                    var statusText = "";
                    
                    switch(dataItem.status) {
                        case "hazırlanıyor":
                            statusClass = "info";
                            statusText = "Hazırlanıyor";
                            break;
                        case "hazır":
                            statusClass = "success";
                            statusText = "Hazır";
                            break;
                        case "etiket_basıldı":
                            statusClass = "primary";
                            statusText = "Etiket Basıldı";
                            break;
                        case "teslim_edildi":
                            statusClass = "default";
                            statusText = "Teslim Edildi";
                            break;
                        default:
                            statusClass = "warning";
                            statusText = dataItem.status;
                    }
                    
                    return "<span class=\'label label-" + statusClass + "\'>" + statusText + "</span>";
                }
            },
            { 
                field: "label_status", 
                title: "Etiket Durumu",
                width: 120,
                template: function(dataItem) {
                    var labelClass = "";
                    var labelText = "";
                    
                    switch(dataItem.label_status) {
                        case "indirilmedi":
                            labelClass = "warning";
                            labelText = "İndirilmedi";
                            break;
                        case "indirildi":
                            labelClass = "info";
                            labelText = "İndirildi";
                            break;
                        case "basıldı":
                            labelClass = "success";
                            labelText = "Basıldı";
                            break;
                        case "kayıp":
                            labelClass = "danger";
                            labelText = "Kayıp";
                            break;
                        case "tekrar_basıldı":
                            labelClass = "primary";
                            labelText = "Tekrar Basıldı";
                            break;
                        default:
                            labelClass = "default";
                            labelText = dataItem.label_status || "Belirsiz";
                    }
                    
                    return "<span class=\'label label-" + labelClass + "\'>" + labelText + "</span>";
                }
            },
            { 
                field: "created_at", 
                title: "Oluşturulma Tarihi",
                width: 150,
                template: "#= kendo.toString(kendo.parseDate(created_at), \'dd.MM.yyyy HH:mm\') #"
            },
            { 
                title: "İşlemler",
                width: 120,
                template: "<a href=\'' . url('/boxes/view/') . '/#=id#\' class=\'btn btn-xs blue\'><i class=\'fa fa-search\'></i> Detaylar</a>"
            }
        ]
    });
    
    // Resim yükleme fonksiyonu
    function uploadModelImage(modelId, modelKodu) {
        // Modal oluştur
        var $modal = $("<div class=\'modal fade\' id=\'uploadImageModal\' tabindex=\'-1\' role=\'dialog\' aria-labelledby=\'uploadImageModalLabel\'>" +
            "<div class=\'modal-dialog\' role=\'document\'>" +
                "<div class=\'modal-content\'>" +
                    "<div class=\'modal-header\'>" +
                        "<button type=\'button\' class=\'close\' data-dismiss=\'modal\' aria-label=\'Close\'><span aria-hidden=\'true\'>&times;</span></button>" +
                        "<h4 class=\'modal-title\' id=\'uploadImageModalLabel\'>" + modelKodu + " - Resim Ekle</h4>" +
                    "</div>" +
                    "<div class=\'modal-body\'>" +
                        "<form id=\'uploadImageForm\' enctype=\'multipart/form-data\'>" +
                            "<input type=\'hidden\' name=\'model_id\' value=\'" + modelId + "\'>" +
                            "<div class=\'form-group\'>" +
                                "<label for=\'imageFile\'>Resim Seçin:</label>" +
                                "<input type=\'file\' class=\'form-control\' id=\'imageFile\' name=\'image\' accept=\'image/*\'>" +
                            "</div>" +
                        "</form>" +
                    "</div>" +
                    "<div class=\'modal-footer\'>" +
                        "<button type=\'button\' class=\'btn btn-default\' data-dismiss=\'modal\'>İptal</button>" +
                        "<button type=\'button\' class=\'btn btn-primary\' id=\'saveImageBtn\'>Kaydet</button>" +
                    "</div>" +
                "</div>" +
            "</div>" +
        "</div>");
        
        // Modal\'ı göster
        $("body").append($modal);
        $modal.modal("show");
        
        // Modal kapatıldığında temizle
        $modal.on("hidden.bs.modal", function() {
            $modal.remove();
        });
        
        // Kaydet butonu
        $("#saveImageBtn").click(function() {
            var formData = new FormData($("#uploadImageForm")[0]);
            
            $.ajax({
                url: "' . url('/ajax/upload-model-image') . '",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $modal.modal("hide");
                        alert("Resim başarıyla yüklendi.");
                        
                        // Sayfayı yenile
                        location.reload();
                    } else {
                        alert("Hata: " + response.error);
                    }
                },
                error: function() {
                    alert("Resim yüklenirken bir hata oluştu.");
                }
            });
        });
    }
';

// Sayfa özel stil
$styles = ['x-editable.css'];
$scripts = ['bootstrap-editable.min.js'];
?>