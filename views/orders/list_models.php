<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Model Listesi</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
                <div class="actions">
                    <a href="<?= url('/orders/import') ?>" class="btn btn-circle btn-default">
                        <i class="fa fa-upload"></i> Veri İçe Aktar
                    </a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <div id="modelGrid"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Sayfa özel script
$pageScript = '
    // Model verilerini hazırla
    var modelData = ' . json_encode($models) . ';
    
    // Kendo UI Grid
    $("#modelGrid").kendoGrid({
        dataSource: {
            data: modelData,
            schema: {
                model: {
                    fields: {
                        id: { type: "number" },
                        model_kodu: { type: "string" },
                        image_path: { type: "string" },
                        order_count: { type: "number" },
                        box_count: { type: "number" },
                        created_at: { type: "date" },
                        updated_at: { type: "date" }
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
                field: "image_path", 
                title: "Resim",
                width: 100,
                template: function(dataItem) {
                    if (dataItem.image_path) {
                        return "<img src=\'" + dataItem.image_path + "\' width=\'50\' />";
                    } else {
                        return "<span class=\'label label-sm label-warning\'>Resim Yok</span>";
                    }
                }
            },
            { 
                field: "order_count", 
                title: "Sipariş Sayısı",
                width: 120
            },
            { 
                field: "box_count", 
                title: "Koli Sayısı",
                width: 120
            },
            { 
                field: "created_at", 
                title: "Oluşturulma Tarihi",
                width: 150,
                template: "#= kendo.toString(kendo.parseDate(created_at), \'dd.MM.yyyy HH:mm\') #"
            },
            { 
                field: "updated_at", 
                title: "Güncelleme Tarihi",
                width: 150,
                template: "#= kendo.toString(kendo.parseDate(updated_at), \'dd.MM.yyyy HH:mm\') #"
            },
            { 
                title: "İşlemler",
                width: 180,
                template: "<a href=\'' . url('/orders/view/') . '/#=model_kodu#\' class=\'btn btn-xs blue\'><i class=\'fa fa-search\'></i> Detaylar</a> " +
                          "<a href=\'javascript:;\' onclick=\'uploadImage(#=id#, \"#=model_kodu#\")\' class=\'btn btn-xs green\'><i class=\'fa fa-image\'></i> Resim Ekle</a>"
            }
        ]
    });
    
    // Resim ekleme fonksiyonu
    function uploadImage(modelId, modelKodu) {
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


?>

<!-- Resim Yükleme Modal Template -->
<div class="modal fade" id="uploadImageModalTemplate" tabindex="-1" role="dialog" aria-labelledby="uploadImageModalLabel" style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="uploadImageModalLabel">Resim Ekle</h4>
            </div>
            <div class="modal-body">
                <form id="uploadImageForm" enctype="multipart/form-data">
                    <input type="hidden" name="model_id" id="modelIdInput">
                    <div class="form-group">
                        <label for="imageFile">Resim Seçin:</label>
                        <input type="file" class="form-control" id="imageFile" name="image" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="saveImageBtn">Kaydet</button>
            </div>
        </div>
    </div>
</div>