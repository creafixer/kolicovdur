<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cubes font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Koli Listesi</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
                <div class="actions">
                    <a href="<?= url('/boxes/calculate') ?>" class="btn btn-circle btn-default">
                        <i class="fa fa-calculator"></i> Koli Hesapla
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <!-- Filtreler -->
                <div class="row margin-bottom-10">
                    <div class="col-md-12">
                        <form action="<?= url('/boxes/list') ?>" method="get" class="form-horizontal">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Model Kodu:</label>
                                            <div class="col-md-8">
                                                <input type="text" name="model_kodu" class="form-control" value="<?= $filters['model_kodu'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Sipariş No:</label>
                                            <div class="col-md-8">
                                                <input type="text" name="siparis_numarasi" class="form-control" value="<?= $filters['siparis_numarasi'] ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Durum:</label>
                                            <div class="col-md-8">
                                                <select name="status" class="form-control">
                                                    <option value="">Tümü</option>
                                                    <option value="hazırlanıyor" <?= $filters['status'] === 'hazırlanıyor' ? 'selected' : '' ?>>Hazırlanıyor</option>
                                                    <option value="hazır" <?= $filters['status'] === 'hazır' ? 'selected' : '' ?>>Hazır</option>
                                                    <option value="etiket_basıldı" <?= $filters['status'] === 'etiket_basıldı' ? 'selected' : '' ?>>Etiket Basıldı</option>
                                                    <option value="teslim_edildi" <?= $filters['status'] === 'teslim_edildi' ? 'selected' : '' ?>>Teslim Edildi</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-12 text-right">
                                        <button type="submit" class="btn blue">
                                            <i class="fa fa-search"></i> Filtrele
                                        </button>
                                        <a href="<?= url('/boxes/list') ?>" class="btn default">
                                            <i class="fa fa-times"></i> Temizle
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Koli Listesi -->
                <div class="row">
                    <div class="col-md-12">
                        <?php if (empty($boxes)): ?>
                            <div class="alert alert-info">
                                <strong>Bilgi:</strong> Filtrelere uygun koli bulunamadı.
                            </div>
                        <?php else: ?>
                            <div id="boxGrid" class="custom-grid"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Sayfa özel script
$pageScript = '
    // Koli Grid
    var boxData = ' . (!empty($boxes) ? json_encode($boxes) : '[]') . ';
    
    $("#boxGrid").kendoGrid({
        dataSource: {
            data: boxData,
            schema: {
                model: {
                    fields: {
                        id: { type: "number" },
                        model_kodu: { type: "string" },
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
            pageSize: 20
        },
        height: 550,
        sortable: true,
        filterable: true,
        pageable: {
            refresh: true,
            pageSizes: [10, 20, 50, 100],
            buttonCount: 5
        },
        columns: [
            { 
                field: "model_kodu", 
                title: "Model Kodu",
                width: 150
            },
            { 
                field: "siparis_numarasi", 
                title: "Sipariş No",
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
                title: "Lot",
                width: 80
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
                width: 220,
                template: function(dataItem) {
                    var html = "<a href=\'' . url('/boxes/view/') . '/#=id#\' class=\'btn btn-xs blue\'><i class=\'fa fa-search\'></i> Detaylar</a> ";
                    
                    // Durum değiştirme butonu
                    html += "<a href=\'javascript:;\' onclick=\'updateBoxStatus(#=id#, \"#=status#\")\' class=\'btn btn-xs green\'><i class=\'fa fa-refresh\'></i> Durum</a> ";
                    
                    // Etiket durumu değiştirme butonu
                    html += "<a href=\'javascript:;\' onclick=\'updateLabelStatus(#=id#, \"#=label_status#\")\' class=\'btn btn-xs purple\'><i class=\'fa fa-tag\'></i> Etiket</a>";
                    
                    return html;
                }
            }
        ]
    });
    
    // Koli durumunu güncelleme fonksiyonu
    function updateBoxStatus(boxId, currentStatus) {
        // Modal oluştur
        var $modal = $("<div class=\'modal fade\' id=\'updateStatusModal\' tabindex=\'-1\' role=\'dialog\' aria-labelledby=\'updateStatusModalLabel\'>" +
            "<div class=\'modal-dialog\' role=\'document\'>" +
                "<div class=\'modal-content\'>" +
                    "<div class=\'modal-header\'>" +
                        "<button type=\'button\' class=\'close\' data-dismiss=\'modal\' aria-label=\'Close\'><span aria-hidden=\'true\'>&times;</span></button>" +
                        "<h4 class=\'modal-title\' id=\'updateStatusModalLabel\'>Koli Durumunu Güncelle</h4>" +
                    "</div>" +
                    "<div class=\'modal-body\'>" +
                        "<form id=\'updateStatusForm\'>" +
                            "<input type=\'hidden\' name=\'box_id\' value=\'" + boxId + "\'>" +
                            "<div class=\'form-group\'>" +
                                "<label for=\'statusSelect\'>Durum:</label>" +
                                "<select class=\'form-control\' id=\'statusSelect\' name=\'status\'>" +
                                    "<option value=\'hazırlanıyor\'" + (currentStatus === "hazırlanıyor" ? " selected" : "") + ">Hazırlanıyor</option>" +
                                    "<option value=\'hazır\'" + (currentStatus === "hazır" ? " selected" : "") + ">Hazır</option>" +
                                    "<option value=\'etiket_basıldı\'" + (currentStatus === "etiket_basıldı" ? " selected" : "") + ">Etiket Basıldı</option>" +
                                    "<option value=\'teslim_edildi\'" + (currentStatus === "teslim_edildi" ? " selected" : "") + ">Teslim Edildi</option>" +
                                "</select>" +
                            "</div>" +
                        "</form>" +
                    "</div>" +
                    "<div class=\'modal-footer\'>" +
                        "<button type=\'button\' class=\'btn btn-default\' data-dismiss=\'modal\'>İptal</button>" +
                        "<button type=\'button\' class=\'btn btn-primary\' id=\'saveStatusBtn\'>Kaydet</button>" +
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
        $("#saveStatusBtn").click(function() {
            var formData = $("#updateStatusForm").serialize();
            
            $.ajax({
                url: "' . url('/boxes/update-status') . '",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $modal.modal("hide");
                        alert("Koli durumu başarıyla güncellendi.");
                        
                        // Sayfayı yenile
                        location.reload();
                    } else {
                        alert("Hata: " + response.error);
                    }
                },
                error: function() {
                    alert("Koli durumu güncellenirken bir hata oluştu.");
                }
            });
        });
    }
    
    // Etiket durumunu güncelleme fonksiyonu
    function updateLabelStatus(boxId, currentStatus) {
        // Modal oluştur
        var $modal = $("<div class=\'modal fade\' id=\'updateLabelModal\' tabindex=\'-1\' role=\'dialog\' aria-labelledby=\'updateLabelModalLabel\'>" +
            "<div class=\'modal-dialog\' role=\'document\'>" +
                "<div class=\'modal-content\'>" +
                    "<div class=\'modal-header\'>" +
                        "<button type=\'button\' class=\'close\' data-dismiss=\'modal\' aria-label=\'Close\'><span aria-hidden=\'true\'>&times;</span></button>" +
                        "<h4 class=\'modal-title\' id=\'updateLabelModalLabel\'>Etiket Durumunu Güncelle</h4>" +
                    "</div>" +
                    "<div class=\'modal-body\'>" +
                        "<form id=\'updateLabelForm\'>" +
                            "<input type=\'hidden\' name=\'box_id\' value=\'" + boxId + "\'>" +
                            "<div class=\'form-group\'>" +
                                "<label for=\'labelStatusSelect\'>Etiket Durumu:</label>" +
                                "<select class=\'form-control\' id=\'labelStatusSelect\' name=\'label_status\'>" +
                                    "<option value=\'indirilmedi\'" + (currentStatus === "indirilmedi" ? " selected" : "") + ">İndirilmedi</option>" +
                                    "<option value=\'indirildi\'" + (currentStatus === "indirildi" ? " selected" : "") + ">İndirildi</option>" +
                                    "<option value=\'basıldı\'" + (currentStatus === "basıldı" ? " selected" : "") + ">Basıldı</option>" +
                                    "<option value=\'kayıp\'" + (currentStatus === "kayıp" ? " selected" : "") + ">Kayıp</option>" +
                                    "<option value=\'tekrar_basıldı\'" + (currentStatus === "tekrar_basıldı" ? " selected" : "") + ">Tekrar Basıldı</option>" +
                                "</select>" +
                            "</div>" +
                            "<div class=\'form-group\'>" +
                                "<label for=\'teslimKisi\'>Teslim Edilen Kişi:</label>" +
                                "<input type=\'text\' class=\'form-control\' id=\'teslimKisi\' name=\'teslim_edilen_kisi\'>" +
                            "</div>" +
                            "<div class=\'form-group\'>" +
                                "<label for=\'teslimTarihi\'>Teslim Tarihi:</label>" +
                                "<input type=\'datetime-local\' class=\'form-control\' id=\'teslimTarihi\' name=\'teslim_tarihi\'>" +
                            "</div>" +
                            "<div class=\'form-group\'>" +
                                "<label for=\'teslimAdet\'>Teslim Adet:</label>" +
                                "<input type=\'number\' class=\'form-control\' id=\'teslimAdet\' name=\'teslim_adet\' min=\'0\'>" +
                            "</div>" +
                            "<div class=\'form-group\'>" +
                                "<label for=\'notes\'>Notlar:</label>" +
                                "<textarea class=\'form-control\' id=\'notes\' name=\'notes\' rows=\'3\'></textarea>" +
                            "</div>" +
                        "</form>" +
                    "</div>" +
                    "<div class=\'modal-footer\'>" +
                        "<button type=\'button\' class=\'btn btn-default\' data-dismiss=\'modal\'>İptal</button>" +
                        "<button type=\'button\' class=\'btn btn-primary\' id=\'saveLabelBtn\'>Kaydet</button>" +
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
        $("#saveLabelBtn").click(function() {
            var formData = $("#updateLabelForm").serialize();
            
            $.ajax({
                url: "' . url('/boxes/update-label-status') . '",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $modal.modal("hide");
                        alert("Etiket durumu başarıyla güncellendi.");
                        
                        // Sayfayı yenile
                        location.reload();
                    } else {
                        alert("Hata: " + response.error);
                    }
                },
                error: function() {
                    alert("Etiket durumu güncellenirken bir hata oluştu.");
                }
            });
        });
    }
';


?>