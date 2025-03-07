<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-upload font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Excel Verisi İçe Aktar</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
            </div>
            <div class="portlet-body form">
                <div class="alert alert-info">
                    <strong>Bilgi:</strong> Excel dosyasından verileri kopyalayıp aşağıdaki alana yapıştırın. Veriler önce önizlenir, onayladıktan sonra kaydedilir.
                </div>
                
                <div class="form-body">
                    <form id="importForm" class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-md-3">Excel Verisi:</label>
                            <div class="col-md-9">
                                <textarea id="excelData" class="form-control" rows="10" placeholder="Excel'den kopyalanan verileri buraya yapıştırın"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="button" id="previewBtn" class="btn blue">
                                        <i class="fa fa-eye"></i> Önizle
                                    </button>
                                    <button type="button" id="clearBtn" class="btn default">
                                        <i class="fa fa-times"></i> Temizle
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div id="previewContainer" class="margin-top-20" style="display:none;">
                    <h3>Veri Önizlemesi</h3>
                    <div id="previewContent"></div>
                    
                    <div class="margin-top-20">
                        <button type="button" id="saveDataBtn" class="btn green">
                            <i class="fa fa-save"></i> Verileri Kaydet
                        </button>
                        <button type="button" id="cancelBtn" class="btn default">
                            <i class="fa fa-times"></i> İptal
                        </button>
                    </div>
                </div>
                
                <div id="loadingContainer" class="text-center margin-top-20" style="display:none;">
                    <img src="<?= asset_url('images/loading.gif') ?>" alt="Yükleniyor...">
                    <p>Veriler işleniyor, lütfen bekleyin...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Sayfa özel script
$pageScript = '
    // Önizleme butonu
    $("#previewBtn").click(function() {
        var excelData = $("#excelData").val();
        
        if (!excelData) {
            alert("Lütfen Excel verilerini yapıştırın.");
            return;
        }
        
        // Yükleniyor göster
        $("#loadingContainer").show();
        $("#previewBtn").prop("disabled", true);
        
        // AJAX isteği
        $.ajax({
            url: "' . url('/orders/process-import') . '",
            type: "POST",
            data: {
                excel_data: excelData
            },
            dataType: "json",
            success: function(response) {
                $("#loadingContainer").hide();
                $("#previewBtn").prop("disabled", false);
                
                if (response.success) {
                    // Önizleme içeriğini göster
                    $("#previewContent").html(response.preview);
                    $("#previewContainer").show();
                } else {
                    alert("Hata: " + response.error);
                }
            },
            error: function() {
                $("#loadingContainer").hide();
                $("#previewBtn").prop("disabled", false);
                alert("Veriler işlenirken bir hata oluştu.");
            }
        });
    });
    
    // Temizle butonu
    $("#clearBtn").click(function() {
        $("#excelData").val("");
        $("#previewContainer").hide();
    });
    
    // İptal butonu
    $("#cancelBtn").click(function() {
        $("#previewContainer").hide();
    });
    
    // Kaydet butonu
    $("#saveDataBtn").click(function() {
        var excelData = $("#excelData").val();
        
        if (!excelData) {
            alert("Lütfen Excel verilerini yapıştırın.");
            return;
        }
        
        // Onay iste
        if (!confirm("Verileri kaydetmek istediğinize emin misiniz?")) {
            return;
        }
        
        // Yükleniyor göster
        $("#loadingContainer").show();
        $("#saveDataBtn").prop("disabled", true);
        
        // AJAX isteği
        $.ajax({
            url: "' . url('/orders/process-import') . '",
            type: "POST",
            data: {
                excel_data: excelData,
                confirmed: true
            },
            dataType: "json",
            success: function(response) {
                $("#loadingContainer").hide();
                $("#saveDataBtn").prop("disabled", false);
                
                if (response.success) {
                    alert("Başarılı: " + response.message);
                    $("#excelData").val("");
                    $("#previewContainer").hide();
                } else {
                    alert("Hata: " + response.error);
                }
            },
            error: function() {
                $("#loadingContainer").hide();
                $("#saveDataBtn").prop("disabled", false);
                alert("Veriler kaydedilirken bir hata oluştu.");
            }
        });
    });
';

// Sayfa özel stil
$styles = ['import.css'];
?>