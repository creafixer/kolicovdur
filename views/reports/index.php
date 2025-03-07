<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart-o font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Raporlar</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
                <div class="actions">
                    <div class="btn-group">
                        <a class="btn btn-sm btn-default dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                            <i class="fa fa-download"></i> Dışa Aktar <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a href="<?= url('/reports/export?type=' . $reportType . '&format=excel' . $this->buildQueryString($filters)) ?>">
                                    <i class="fa fa-file-excel-o"></i> Excel
                                </a>
                            </li>
                            <li>
                                <a href="<?= url('/reports/export?type=' . $reportType . '&format=pdf' . $this->buildQueryString($filters)) ?>">
                                    <i class="fa fa-file-pdf-o"></i> PDF
                                </a>
                            </li>
                            <li>
                                <a href="<?= url('/reports/export?type=' . $reportType . '&format=csv' . $this->buildQueryString($filters)) ?>">
                                    <i class="fa fa-file-text-o"></i> CSV
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="portlet-body">
                <!-- Rapor Tipleri -->
                <div class="row margin-bottom-20">
                    <div class="col-md-12">
                        <ul class="nav nav-pills">
                            <li class="<?= $reportType === 'general' ? 'active' : '' ?>">
                                <a href="<?= url('/reports?type=general') ?>">Genel Rapor</a>
                            </li>
                            <li class="<?= $reportType === 'model' ? 'active' : '' ?>">
                                <a href="<?= url('/reports?type=model') ?>">Model Raporu</a>
                            </li>
                            <li class="<?= $reportType === 'order' ? 'active' : '' ?>">
                                <a href="<?= url('/reports?type=order') ?>">Sipariş Raporu</a>
                            </li>
                            <li class="<?= $reportType === 'box' ? 'active' : '' ?>">
                                <a href="<?= url('/reports?type=box') ?>">Koli Raporu</a>
                            </li>
                            <li class="<?= $reportType === 'label' ? 'active' : '' ?>">
                                <a href="<?= url('/reports?type=label') ?>">Etiket Raporu</a>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Filtreler -->
                <?php if ($reportType !== 'general'): ?>
                    <div class="row margin-bottom-20">
                        <div class="col-md-12">
                            <div class="portlet box blue-hoki">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-filter"></i>Filtreler
                                    </div>
                                    <div class="tools">
                                        <a href="javascript:;" class="collapse"></a>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <form action="<?= url('/reports') ?>" method="get" class="form-horizontal">
                                        <input type="hidden" name="type" value="<?= $reportType ?>">
                                        
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
                                                        <label class="control-label col-md-4">Teslimat Ülkesi:</label>
                                                        <div class="col-md-8">
                                                            <input type="text" name="teslimat_ulkesi" class="form-control" value="<?= $filters['teslimat_ulkesi'] ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Başlangıç Tarihi:</label>
                                                        <div class="col-md-8">
                                                            <input type="date" name="tarih_baslangic" class="form-control" value="<?= $filters['tarih_baslangic'] ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Bitiş Tarihi:</label>
                                                        <div class="col-md-8">
                                                            <input type="date" name="tarih_bitis" class="form-control" value="<?= $filters['tarih_bitis'] ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Durum:</label>
                                                        <div class="col-md-8">
                                                            <select name="durum" class="form-control">
                                                                <option value="">Tümü</option>
                                                                <?php if ($reportType === 'order'): ?>
                                                                    <option value="completed" <?= $filters['durum'] === 'completed' ? 'selected' : '' ?>>Tamamlandı</option>
                                                                    <option value="pending" <?= $filters['durum'] === 'pending' ? 'selected' : '' ?>>Devam Ediyor</option>
                                                                <?php elseif ($reportType === 'box'): ?>
                                                                    <option value="hazırlanıyor" <?= $filters['durum'] === 'hazırlanıyor' ? 'selected' : '' ?>>Hazırlanıyor</option>
                                                                    <option value="hazır" <?= $filters['durum'] === 'hazır' ? 'selected' : '' ?>>Hazır</option>
                                                                    <option value="etiket_basıldı" <?= $filters['durum'] === 'etiket_basıldı' ? 'selected' : '' ?>>Etiket Basıldı</option>
                                                                    <option value="teslim_edildi" <?= $filters['durum'] === 'teslim_edildi' ? 'selected' : '' ?>>Teslim Edildi</option>
                                                                <?php elseif ($reportType === 'label'): ?>
                                                                    <option value="indirilmedi" <?= $filters['durum'] === 'indirilmedi' ? 'selected' : '' ?>>İndirilmedi</option>
                                                                    <option value="indirildi" <?= $filters['durum'] === 'indirildi' ? 'selected' : '' ?>>İndirildi</option>
                                                                    <option value="basıldı" <?= $filters['durum'] === 'basıldı' ? 'selected' : '' ?>>Basıldı</option>
                                                                    <option value="kayıp" <?= $filters['durum'] === 'kayıp' ? 'selected' : '' ?>>Kayıp</option>
                                                                    <option value="tekrar_basıldı" <?= $filters['durum'] === 'tekrar_basıldı' ? 'selected' : '' ?>>Tekrar Basıldı</option>
                                                                <?php endif; ?>
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
                                                    <a href="<?= url('/reports?type=' . $reportType) ?>" class="btn default">
                                                        <i class="fa fa-times"></i> Temizle
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Rapor İçeriği -->
                <div class="row">
                    <div class="col-md-12">
                        <?php if ($reportType === 'general'): ?>
                            <!-- Genel Rapor -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="portlet solid bordered grey-cararra">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-bar-chart-o"></i>Genel İstatistikler
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered table-hover">
                                                    <tr>
                                                        <th>Toplam Model Sayısı</th>
                                                        <td><?= number_format($data['model_stats']['total']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Resimli Model Sayısı</th>
                                                        <td><?= number_format($data['model_stats']['with_images']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Son 30 Gün İçinde Eklenen Model</th>
                                                        <td><?= number_format($data['model_stats']['last_month']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Toplam Sipariş Sayısı</th>
                                                        <td><?= number_format($data['order_stats']['total_orders']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Toplam Lot Sayısı</th>
                                                        <td><?= number_format($data['order_stats']['total_lots']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Depo Girişi Olan Lot Sayısı</th>
                                                        <td><?= number_format($data['order_stats']['total_deposited_lots']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tamamlanan Sipariş Sayısı</th>
                                                        <td><?= number_format($data['order_stats']['completed_orders']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Toplam Koli Sayısı</th>
                                                        <td><?= number_format($data['box_stats']['total_boxes']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tam Koli Sayısı</th>
                                                        <td><?= number_format($data['box_stats']['full_boxes']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Kırık Koli Sayısı</th>
                                                        <td><?= number_format($data['box_stats']['partial_boxes']) ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="portlet solid bordered grey-cararra">
                                        <div class="portlet-title">
                                            <div class="caption">
                                                <i class="fa fa-pie-chart"></i>Grafikler
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div id="lotChart" style="height: 250px;"></div>
                                            <div id="countryChart" style="height: 250px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <!-- Diğer Raporlar -->
                            <div id="reportGrid"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Grafik verileri hazırla
if ($reportType === 'general') {
    $lotData = [
        ['category' => 'Sipariş Edilen', 'value' => $data['order_stats']['total_lots']],
        ['category' => 'Depo Girişi Olan', 'value' => $data['order_stats']['total_deposited_lots']],
        ['category' => 'Kalan', 'value' => $data['order_stats']['total_lots'] - $data['order_stats']['total_deposited_lots']]
    ];

    $countryData = [];
    foreach ($data['order_stats']['countries_distribution'] as $country) {
        $countryData[] = [
            'category' => $country['teslimat_ulkesi'],
            'value' => $country['count']
        ];
    }
}

// Sayfa özel script
$pageScript = '';

if ($reportType === 'general') {
    $pageScript .= '
        // Lot Durumu Grafiği
        $("#lotChart").kendoChart({
            title: {
                text: "Lot Durumu"
            },
            legend: {
                position: "bottom"
            },
            seriesDefaults: {
                type: "column"
            },
            series: [{
                name: "Lot Sayısı",
                data: [' . $data['order_stats']['total_lots'] . ', ' . $data['order_stats']['total_deposited_lots'] . ', ' . ($data['order_stats']['total_lots'] - $data['order_stats']['total_deposited_lots']) . '],
                color: "#007bff"
            }],
            valueAxis: {
                labels: {
                    format: "{0}"
                },
                title: {
                    text: "Lot Sayısı"
                }
            },
            categoryAxis: {
                categories: ["Sipariş Edilen", "Depo Girişi Olan", "Kalan"],
                majorGridLines: {
                    visible: false
                }
            },
            tooltip: {
                visible: true,
                format: "{0} lot",
                template: "#= category #: #= value #"
            }
        });
        
        // Teslimat Ülkeleri Grafiği
        $("#countryChart").kendoChart({
            title: {
                text: "Teslimat Ülkeleri Dağılımı"
            },
            legend: {
                position: "bottom"
            },
            seriesDefaults: {
                type: "pie"
            },
            series: [{
                name: "Sipariş Sayısı",
                data: ' . json_encode($countryData) . '
            }],
            tooltip: {
                visible: true,
                template: "#= category #: #= value # sipariş"
            }
        });
    ';
} else {
    // Rapor tiplerine göre grid kolonlarını ayarla
    $columns = [];
    
    switch ($reportType) {
        case 'model':
            $columns = [
                ['field' => 'model_kodu', 'title' => 'Model Kodu', 'width' => 150],
                ['field' => 'image_path', 'title' => 'Resim', 'width' => 100, 'template' => "# if (image_path) { # <span class='label label-success'>Var</span> # } else { # <span class='label label-warning'>Yok</span> # } #"],
                ['field' => 'order_count', 'title' => 'Sipariş Sayısı', 'width' => 120],
                ['field' => 'box_count', 'title' => 'Koli Sayısı', 'width' => 120],
                ['field' => 'total_lots', 'title' => 'Toplam Lot', 'width' => 120],
                ['field' => 'total_deposited_lots', 'title' => 'Depo Girişi Lot', 'width' => 120],
                ['field' => 'created_at', 'title' => 'Oluşturulma Tarihi', 'width' => 150, 'template' => "#= kendo.toString(kendo.parseDate(created_at), 'dd.MM.yyyy HH:mm') #"],
                ['field' => 'updated_at', 'title' => 'Güncelleme Tarihi', 'width' => 150, 'template' => "#= kendo.toString(kendo.parseDate(updated_at), 'dd.MM.yyyy HH:mm') #"]
            ];
            break;
            
        case 'order':
            $columns = [
                ['field' => 'model_kodu', 'title' => 'Model Kodu', 'width' => 150],
                ['field' => 'sezon', 'title' => 'Sezon', 'width' => 100],
                ['field' => 'siparis_numarasi', 'title' => 'Sipariş No', 'width' => 120],
                ['field' => 'teslimat_ulkesi', 'title' => 'Teslimat Ülkesi', 'width' => 120],
                ['field' => 'siparis_gecilen_lot_sayisi', 'title' => 'Sipariş Lot', 'width' => 100],
                ['field' => 'depo_girisi_olan_lot_sayisi', 'title' => 'Depo Giriş Lot', 'width' => 100],
                ['field' => 'remaining_lots', 'title' => 'Kalan Lot', 'width' => 100],
                ['field' => 'is_completed', 'title' => 'Durum', 'width' => 120, 'template' => "# if (is_completed) { # <span class='label label-success'>Tamamlandı</span> # } else { # <span class='label label-warning'>Devam Ediyor</span> # } #"],
                ['field' => 'created_at', 'title' => 'Oluşturulma Tarihi', 'width' => 150, 'template' => "#= kendo.toString(kendo.parseDate(created_at), 'dd.MM.yyyy HH:mm') #"]
            ];
            break;
            
        case 'box':
            $columns = [
                ['field' => 'model_kodu', 'title' => 'Model Kodu', 'width' => 150],
                ['field' => 'siparis_numarasi', 'title' => 'Sipariş No', 'width' => 120],
                ['field' => 'teslimat_ulkesi', 'title' => 'Teslimat Ülkesi', 'width' => 120],
                ['field' => 'lot_kodu', 'title' => 'Lot Kodu', 'width' => 120],
                ['field' => 'box_type', 'title' => 'Koli Tipi', 'width' => 100, 'template' => "# if (box_type === 'tam') { # <span class='label label-success'>Tam Koli</span> # } else { # <span class='label label-warning'>Kırık Koli</span> # } #"],
                ['field' => 'box_number', 'title' => 'Koli No', 'width' => 80],
                ['field' => 'lot_count', 'title' => 'Lot Sayısı', 'width' => 80],
                ['field' => 'status', 'title' => 'Durum', 'width' => 120, 'template' => "# var statusClass = ''; var statusText = ''; switch(status) { case 'hazırlanıyor': statusClass = 'info'; statusText = 'Hazırlanıyor'; break; case 'hazır': statusClass = 'success'; statusText = 'Hazır'; break; case 'etiket_basıldı': statusClass = 'primary'; statusText = 'Etiket Basıldı'; break; case 'teslim_edildi': statusClass = 'default'; statusText = 'Teslim Edildi'; break; default: statusClass = 'warning'; statusText = status; } # <span class='label label-#= statusClass #'>#= statusText #</span>"],
                ['field' => 'label_status', 'title' => 'Etiket Durumu', 'width' => 120, 'template' => "# var labelClass = ''; var labelText = ''; switch(label_status) { case 'indirilmedi': labelClass = 'warning'; labelText = 'İndirilmedi'; break; case 'indirildi': labelClass = 'info'; labelText = 'İndirildi'; break; case 'basıldı': labelClass = 'success'; labelText = 'Basıldı'; break; case 'kayıp': labelClass = 'danger'; labelText = 'Kayıp'; break; case 'tekrar_basıldı': labelClass = 'primary'; labelText = 'Tekrar Basıldı'; break; default: labelClass = 'default'; labelText = label_status || 'Belirsiz'; } # <span class='label label-#= labelClass #'>#= labelText #</span>"],
                ['field' => 'created_at', 'title' => 'Oluşturulma Tarihi', 'width' => 150, 'template' => "#= kendo.toString(kendo.parseDate(created_at), 'dd.MM.yyyy HH:mm') #"]
            ];
            break;
            
        case 'label':
            $columns = [
                ['field' => 'model_kodu', 'title' => 'Model Kodu', 'width' => 150],
                ['field' => 'siparis_numarasi', 'title' => 'Sipariş No', 'width' => 120],
                ['field' => 'teslimat_ulkesi', 'title' => 'Teslimat Ülkesi', 'width' => 120],
                ['field' => 'lot_kodu', 'title' => 'Lot Kodu', 'width' => 120],
                ['field' => 'box_type', 'title' => 'Koli Tipi', 'width' => 100, 'template' => "# if (box_type === 'tam') { # <span class='label label-success'>Tam Koli</span> # } else { # <span class='label label-warning'>Kırık Koli</span> # } #"],
                ['field' => 'box_number', 'title' => 'Koli No', 'width' => 80],
                ['field' => 'label_status', 'title' => 'Etiket Durumu', 'width' => 120, 'template' => "# var labelClass = ''; var labelText = ''; switch(label_status) { case 'indirilmedi': labelClass = 'warning'; labelText = 'İndirilmedi'; break; case 'indirildi': labelClass = 'info'; labelText = 'İndirildi'; break; case 'basıldı': labelClass = 'success'; labelText = 'Basıldı'; break; case 'kayıp': labelClass = 'danger'; labelText = 'Kayıp'; break; case 'tekrar_basıldı': labelClass = 'primary'; labelText = 'Tekrar Basıldı'; break; default: labelClass = 'default'; labelText = label_status || 'Belirsiz'; } # <span class='label label-#= labelClass #'>#= labelText #</span>"],
                ['field' => 'teslim_edilen_kisi', 'title' => 'Teslim Edilen Kişi', 'width' => 150],
                ['field' => 'teslim_tarihi', 'title' => 'Teslim Tarihi', 'width' => 150, 'template' => "# if (teslim_tarihi) { # #= kendo.toString(kendo.parseDate(teslim_tarihi), 'dd.MM.yyyy HH:mm') # # } else { # - # } #"],
                ['field' => 'teslim_adet', 'title' => 'Teslim Adet', 'width' => 100],
                ['field' => 'notes', 'title' => 'Notlar', 'width' => 200],
                ['field' => 'created_at', 'title' => 'Oluşturulma Tarihi', 'width' => 150, 'template' => "#= kendo.toString(kendo.parseDate(created_at), 'dd.MM.yyyy HH:mm') #"]
            ];
            break;
    }
    
    $pageScript .= '
        // Rapor Grid
        $("#reportGrid").kendoGrid({
            dataSource: {
                data: ' . json_encode($data) . ',
                schema: {
                    model: {
                        fields: {
                            // Tüm alanları burada tanımlayabilirsiniz
                            created_at: { type: "date" },
                            updated_at: { type: "date" },
                            teslim_tarihi: { type: "date" }
                        }
                    }
                },
                pageSize: 20
            },
            height: 550,
            scrollable: true,
            sortable: true,
            filterable: true,
            pageable: {
                refresh: true,
                pageSizes: [10, 20, 50, 100, "all"],
                buttonCount: 5
            },
            columns: ' . json_encode($columns) . '
        });
    ';
}


/**
 * Filtre sorgusunu oluştur
 * 
 * @param array $filters Filtreler
 * @return string Sorgu dizesi
 */
function buildQueryString($filters) {
    $query = '';
    
    foreach ($filters as $key => $value) {
        if ($value) {
            $query .= '&' . $key . '=' . urlencode($value);
        }
    }
    
    return $query;
}
?>