<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-calculator font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Koli Hesaplama</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Model Kodu:</label>
                            <input id="modelKodu" class="form-control" placeholder="Model kodunu yazın...">
                        </div>
                        
                        <div id="modelResults" style="display:none;" class="margin-bottom-10">
                            <div class="well">
                                <ul id="modelList" class="list-unstyled"></ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Koli İçine Girecek Lot Sayısı:</label>
                            <input type="number" id="koliLotSayisi" class="form-control" value="10" min="1">
                        </div>
                        
                        <div class="form-group">
                            <label>Hesaplama Tipi:</label>
                            <div class="mt-radio-list">
                                <label class="mt-radio">
                                    <input type="radio" name="hesaplamaTipi" value="genel" checked> 
                                    Genel Hesaplama
                                    <span></span>
                                </label>
                                <label class="mt-radio">
                                    <input type="radio" name="hesaplamaTipi" value="siparis"> 
                                    Sipariş Numarasına Özel
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        
                        <div id="siparisSecimi" style="display:none;">
                            <div class="form-group">
                                <label>Sipariş Numarası:</label>
                                <input id="siparisNumarasi" class="form-control" placeholder="Sipariş numarasını yazın...">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row margin-top-10">
                    <div class="col-md-12">
                        <button type="button" id="hesaplaBtn" class="btn blue">
                            <i class="fa fa-calculator"></i> Hesapla
                        </button>
                        <button type="button" id="temizleBtn" class="btn default">
                            <i class="fa fa-times"></i> Temizle
                        </button>
                    </div>
                </div>
                
                <div id="orderDetails" style="display:none;" class="margin-top-20">
                    <h3 id="selectedModelTitle"></h3>
                    
                    <div id="orderTabs">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab_toplam" data-toggle="tab">Toplam Sipariş</a>
                            </li>
                            <li>
                                <a href="#tab_beden" data-toggle="tab">Beden Bazlı Toplam Sipariş</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_toplam">
                                <div class="table-responsive">
                                    <div id="toplamSiparisGrid"></div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_beden">
                                <div class="table-responsive">
                                    <div id="bedenBazliGrid"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="boxCalculations" style="display:none;" class="margin-top-20">
                    <h3>Koli Hesaplamaları</h3>
                    
                    <div class="alert alert-info">
                        <strong>Bilgi:</strong> Koli içine girecek lot sayısı: <span id="selectedLotCount"></span>
                    </div>
                    
                    <div id="boxResults"></div>
                    
                    <div class="margin-top-20">
                        <button type="button" id="saveBoxCalculations" class="btn green">
                            <i class="fa fa-save"></i> Hesaplamaları Kaydet
                        </button>
                        <button type="button" id="printLabels" class="btn blue">
                            <i class="fa fa-print"></i> Etiketleri Yazdır
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
    // Kendo UI Model Kodu ComboBox
    $(document).ready(function() {
        $("#modelKodu").kendoComboBox({
            dataSource: {
                transport: {
                    read: {
                        url: "' . url('/ajax/search-model') . '",
                        dataType: "json"
                    }
                }
            },
            dataTextField: "model_kodu",
            dataValueField: "model_kodu",
            filter: "contains",
            minLength: 1,
            placeholder: "Model kodunu yazın...",
            suggest: true
        });
    });
    
    // Kendo UI Sipariş Numarası ComboBox
    $("#siparisNumarasi").kendoComboBox({
        placeholder: "Sipariş numarasını yazın veya seçin...",
        dataTextField: "siparis_numarasi",
        dataValueField: "siparis_numarasi",
        filter: "contains",
        minLength: 0,
        delay: 300,
        height: 400,
        suggest: true,
        clearButton: true,
        dataSource: {
            transport: {
                read: {
                    url: function() {
                        return "' . url('/ajax/search-order') . '?model_kodu=" + encodeURIComponent($("#modelKodu").val());
                    },
                    dataType: "json"
                }
            },
            serverFiltering: true
        },
        template: "<div class=\'siparis-item\'>" +
                  "<strong>#: siparis_numarasi #</strong>" +
                  "# if (teslimat_ulkesi) { #" +
                  "<br><small class=\'text-muted\'>Ülke: #: teslimat_ulkesi #</small>" +
                  "# } #" +
                  "</div>",
        select: function(e) {
            if (e.dataItem) {
                $("#siparisNumarasi").val(e.dataItem.siparis_numarasi);
            }
        }
    });
    
    // CSS ekleyelim
    $("<style>").text(`
        .model-item, .siparis-item {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .model-item:hover, .siparis-item:hover {
            background-color: #f5f5f5;
        }
        .text-muted {
            color: #999;
        }
    `).appendTo("head");
    
    // Hesaplama tipi değiştiğinde sipariş seçimini göster/gizle
    $("input[name=\'hesaplamaTipi\']").change(function() {
        if ($(this).val() === "siparis") {
            $("#siparisSecimi").show();
        } else {
            $("#siparisSecimi").hide();
            $("#siparisNumarasi").val("");
        }
    });
    
    // Hesapla butonu
    $("#hesaplaBtn").click(function() {
        var modelKodu = $("#modelKodu").val();
        var koliLotSayisi = $("#koliLotSayisi").val();
        var hesaplamaTipi = $("input[name=\'hesaplamaTipi\']:checked").val();
        var siparisNumarasi = $("#siparisNumarasi").val();
        
        if (!modelKodu) {
            alert("Lütfen model kodu girin.");
            return;
        }
        
        if (hesaplamaTipi === "siparis" && !siparisNumarasi) {
            alert("Lütfen sipariş numarası girin.");
            return;
        }
        
        // Yükleniyor göster
        $("#loadingContainer").show();
        $("#hesaplaBtn").prop("disabled", true);
        
        // AJAX isteği
        $.ajax({
            url: "' . url('/boxes/process-calculation') . '",
            type: "POST",
            data: {
                model_kodu: modelKodu,
                koli_lot_sayisi: koliLotSayisi,
                hesaplama_tipi: hesaplamaTipi,
                siparis_numarasi: siparisNumarasi
            },
            dataType: "json",
            success: function(response) {
                $("#loadingContainer").hide();
                $("#hesaplaBtn").prop("disabled", false);
                
                if (response.success) {
                    // Model başlığını göster
                    $("#selectedModelTitle").text("Model: " + modelKodu);
                    
                    // Lot sayısını göster
                    $("#selectedLotCount").text(koliLotSayisi);
                    
                    // Sipariş verilerini göster
                    displayOrderData(response.data);
                    
                    // Koli hesaplamalarını göster
                    displayBoxCalculations(response.data);
                    
                    // Bölümleri göster
                    $("#orderDetails").show();
                    $("#boxCalculations").show();
                } else {
                    alert("Hata: " + response.error);
                }
            },
            error: function() {
                $("#loadingContainer").hide();
                $("#hesaplaBtn").prop("disabled", false);
                alert("Veriler işlenirken bir hata oluştu.");
            }
        });
    });
    
    // Temizle butonu
    $("#temizleBtn").click(function() {
        $("#modelKodu").val("");
        $("#koliLotSayisi").val("10");
        $("input[name=\'hesaplamaTipi\'][value=\'genel\']").prop("checked", true).trigger("change");
        $("#siparisNumarasi").val("");
        $("#orderDetails").hide();
        $("#boxCalculations").hide();
    });
    
    // Sipariş verilerini göster
    function displayOrderData(data) {
        // Toplam Sipariş Grid
        var toplamSiparisColumns = [
            { field: "order.siparis_numarasi", title: "Sipariş No", width: 120 },
            { field: "order.lot_kodu", title: "Lot Kodu", width: 120 },
            { field: "order.teslimat_ulkesi", title: "Ülke", width: 100 },
            { field: "order.siparis_gecilen_lot_sayisi", title: "Sipariş Lot", width: 100 },
            { field: "order.depo_girisi_olan_lot_sayisi", title: "Depo Giriş", width: 100 },
            { field: "calculation.kalan_lot", title: "Kalan Lot", width: 100 },
            { field: "calculation.tam_koli_sayisi", title: "Tam Koli", width: 100 },
            { field: "calculation.kirik_koli_lot_sayisi", title: "Kırık Koli Lot", width: 100 },
            { 
                field: "completed", 
                title: "Durum", 
                width: 120,
                template: function(dataItem) {
                    if (dataItem.completed) {
                        return "<span class=\'label label-success\'>TESLİMAT TAMAMLANMIŞ</span>";
                    } else {
                        return "<span class=\'label label-warning\'>DEVAM EDİYOR</span>";
                    }
                }
            }
        ];
        
        // Beden sütunlarını ekle
        if (data.length > 0 && data[0].order && data[0].order.bedenler && Array.isArray(data[0].order.bedenler)) {
            data[0].order.bedenler.forEach(function(beden, index) {
                if (beden && beden.beden_adi) {
                    toplamSiparisColumns.push({
                        field: "order.bedenler[" + index + "].adet",
                        title: beden.beden_adi,
                        width: 80,
                        template: function(dataItem) {
                            try {
                                return dataItem.order.bedenler[index].adet || 0;
                            } catch (e) {
                                return 0;
                            }
                        }
                    });
                }
            });
        }
        
        $("#toplamSiparisGrid").kendoGrid({
            dataSource: {
                data: data,
                schema: {
                    model: {
                        fields: {
                            "order.siparis_numarasi": { type: "string" },
                            "order.lot_kodu": { type: "string" },
                            "order.teslimat_ulkesi": { type: "string" },
                            "order.siparis_gecilen_lot_sayisi": { type: "number" },
                            "order.depo_girisi_olan_lot_sayisi": { type: "number" },
                            "calculation.kalan_lot": { type: "number" },
                            "calculation.tam_koli_sayisi": { type: "number" },
                            "calculation.kirik_koli_lot_sayisi": { type: "number" },
                            "completed": { type: "boolean" }
                        }
                    }
                },
                pageSize: 10
            },
            height: 400,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: {
                refresh: true,
                pageSizes: [5, 10, 20, "all"],
                buttonCount: 5
            },
            columns: toplamSiparisColumns
        });
        
        // Beden Bazlı Grid için benzer kontroller
        var bedenBazliColumns = [
            { field: "order.siparis_numarasi", title: "Sipariş No", width: 120 },
            { field: "order.lot_kodu", title: "Lot Kodu", width: 120 },
            { field: "order.teslimat_ulkesi", title: "Ülke", width: 100 }
        ];
        
        if (data.length > 0 && data[0].order && data[0].order.bedenler && Array.isArray(data[0].order.bedenler)) {
            data[0].order.bedenler.forEach(function(beden, index) {
                if (beden && beden.beden_adi) {
                    bedenBazliColumns.push({
                        field: "order.bedenler[" + index + "].adet",
                        title: beden.beden_adi,
                        width: 80,
                        template: function(dataItem) {
                            try {
                                return dataItem.order.bedenler[index].adet || 0;
                            } catch (e) {
                                return 0;
                            }
                        }
                    });
                }
            });
        }
        
        $("#bedenBazliGrid").kendoGrid({
            dataSource: {
                data: data,
                schema: {
                    model: {
                        fields: {
                            "order.siparis_numarasi": { type: "string" },
                            "order.lot_kodu": { type: "string" },
                            "order.teslimat_ulkesi": { type: "string" }
                        }
                    }
                },
                pageSize: 10
            },
            height: 400,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: {
                refresh: true,
                pageSizes: [5, 10, 20, "all"],
                buttonCount: 5
            },
            columns: bedenBazliColumns
        });
    }
    
    // Koli hesaplamalarını göster
    function displayBoxCalculations(data) {
        var html = "";
        
        $.each(data, function(index, item) {
            var statusClass = item.completed ? "danger" : "success";
            var statusText = item.completed ? "TESLİMAT TAMAMLANMIŞ" : "DEVAM EDİYOR";
            
            html += "<div class=\'portlet box " + statusClass + "\'>";
            html += "<div class=\'portlet-title\'>";
            html += "<div class=\'caption\'>";
            html += "<i class=\'fa fa-gift\'></i> Sipariş No: " + item.order.siparis_numarasi + " - Lot Kodu: " + item.order.lot_kodu;
            html += "</div>";
            html += "<div class=\'tools\'>";
            html += "<a href=\'javascript:;\' class=\'collapse\'></a>";
            html += "</div>";
            html += "</div>";
            html += "<div class=\'portlet-body\'>";
            
            // Sipariş bilgileri
            html += "<div class=\'row\'>";
            html += "<div class=\'col-md-6\'>";
            html += "<table class=\'table table-bordered table-striped\'>";
            html += "<tr><th>Teslimat Ülkesi</th><td>" + item.order.teslimat_ulkesi + "</td></tr>";
            html += "<tr><th>Sipariş Lot</th><td>" + item.order.siparis_gecilen_lot_sayisi + "</td></tr>";
            html += "<tr><th>Depo Giriş</th><td>" + item.order.depo_girisi_olan_lot_sayisi + "</td></tr>";
            html += "<tr><th>Kalan Lot</th><td>" + item.calculation.kalan_lot + "</td></tr>";
            html += "<tr><th>Bir Lottaki Ürün Sayısı</th><td>" + item.order.bir_lottaki_urun_sayisi + "</td></tr>";
            html += "<tr><th>Durum</th><td><span class=\'label label-" + statusClass + "\'>" + statusText + "</span></td></tr>";
            html += "</table>";
            html += "</div>";
            
            html += "<div class=\'col-md-6\'>";
            html += "<table class=\'table table-bordered table-striped\'>";
            html += "<tr><th>Tam Koli Sayısı</th><td>" + item.calculation.tam_koli_sayisi + "</td></tr>";
            html += "<tr><th>Tam Kolilerdeki Toplam Lot</th><td>" + item.calculation.tam_koli_lot_toplami + "</td></tr>";
            html += "<tr><th>Kırık Koli Lot Sayısı</th><td>" + item.calculation.kirik_koli_lot_sayisi + "</td></tr>";
            html += "<tr><th>Toplam Lot</th><td>" + item.calculation.toplam_lot + "</td></tr>";
            html += "<tr><th>Toplam Adet</th><td>" + item.calculation.toplam_adet + "</td></tr>";
            html += "<tr><th>Koli İçi Lot Sayısı</th><td>" + item.calculation.koli_lot_sayisi + "</td></tr>";
            html += "</table>";
            html += "</div>";
            html += "</div>";
            
            // Koliler
            if (!item.completed) {
                html += "<h4>Hesaplanan Koliler</h4>";
                
                // Tam Koliler
                if (item.calculation.tam_koli_sayisi > 0) {
                    html += "<h5>Tam Koliler</h5>";
                    html += "<div class=\'row\'>";
                    
                    $.each(item.calculation.tam_koliler, function(i, box) {
                        html += "<div class=\'col-md-2 col-sm-4 col-xs-6\'>";
                        html += "<div class=\'panel panel-success\'>";
                        html += "<div class=\'panel-heading text-center\'>";
                        html += "Koli " + box.box_number;
                        html += "</div>";
                        html += "<div class=\'panel-body text-center\'>";
                        html += "<strong>" + box.lot_count + "</strong> Lot<br>";
                        html += "<strong>" + box.adet + "</strong> Adet";
                        html += "</div>";
                        html += "</div>";
                        html += "</div>";
                    });
                    
                    html += "</div>";
                }
                
                // Kırık Koli
                if (item.calculation.kirik_koli_lot_sayisi > 0) {
                    html += "<h5>Kırık Koli</h5>";
                    html += "<div class=\'row\'>";
                    html += "<div class=\'col-md-2 col-sm-4 col-xs-6\'>";
                    html += "<div class=\'panel panel-warning\'>";
                    html += "<div class=\'panel-heading text-center\'>";
                    html += "Koli " + item.calculation.kirik_koli.box_number;
                    html += "</div>";
                    html += "<div class=\'panel-body text-center\'>";
                    html += "<strong>" + item.calculation.kirik_koli.lot_count + "</strong> Lot<br>";
                    html += "<strong>" + item.calculation.kirik_koli.adet + "</strong> Adet";
                    html += "</div>";
                    html += "</div>";
                    html += "</div>";
                    html += "</div>";
                }
            } else {
                html += "<div class=\'alert alert-danger\'>";
                html += "<strong>Bilgi:</strong> Bu sipariş için teslimat tamamlanmıştır. Koli hesaplaması yapılmayacaktır.";
                html += "</div>";
            }
            
            html += "</div>"; // portlet-body
            html += "</div>"; // portlet
        });
        
        $("#boxResults").html(html);
    }
    
    // Hesaplamaları kaydet butonu
    $("#saveBoxCalculations").click(function() {
        var modelKodu = $("#modelKodu").val();
        var koliLotSayisi = $("#koliLotSayisi").val();
        var hesaplamaTipi = $("input[name=\'hesaplamaTipi\']:checked").val();
        var siparisNumarasi = $("#siparisNumarasi").val();
        
        if (!modelKodu) {
            alert("Lütfen model kodu girin.");
            return;
        }
        
        // Onay iste
        if (!confirm("Hesaplanan kolileri kaydetmek istediğinize emin misiniz?")) {
            return;
        }
        
        // Yükleniyor göster
        $("#loadingContainer").show();
        $("#saveBoxCalculations").prop("disabled", true);
        
        // AJAX isteği
        $.ajax({
            url: "' . url('/boxes/process-calculation') . '",
            type: "POST",
            data: {
                model_kodu: modelKodu,
                koli_lot_sayisi: koliLotSayisi,
                hesaplama_tipi: hesaplamaTipi,
                siparis_numarasi: siparisNumarasi,
                save_calculation: true
            },
            dataType: "json",
            success: function(response) {
                $("#loadingContainer").hide();
                $("#saveBoxCalculations").prop("disabled", false);
                
                if (response.success) {
                    alert("Hesaplamalar başarıyla kaydedildi.");
                    
                    // Koli listesi sayfasına yönlendir
                    window.location.href = "' . url('/boxes/list') . '?model_kodu=" + encodeURIComponent(modelKodu);
                } else {
                    alert("Hata: " + response.error);
                }
            },
            error: function() {
                $("#loadingContainer").hide();
                $("#saveBoxCalculations").prop("disabled", false);
                alert("Hesaplamalar kaydedilirken bir hata oluştu.");
            }
        });
    });
    
    // Etiketleri yazdır butonu
    $("#printLabels").click(function() {
        alert("Bu özellik henüz uygulanmamıştır.");
    });
    
    // URL parametrelerini kontrol et
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has("model_kodu")) {
        $("#modelKodu").val(urlParams.get("model_kodu"));
    }
';


?>