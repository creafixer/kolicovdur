<div class="row">
    <!-- İstatistikler -->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat blue">
            <div class="visual">
                <i class="fa fa-shopping-cart"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?= number_format($orders['total_orders']) ?>
                </div>
                <div class="desc">
                    Toplam Sipariş
                </div>
            </div>
            <a class="more" href="<?= url('/orders/list') ?>">
                Detayları Gör <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat green">
            <div class="visual">
                <i class="fa fa-cube"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?= number_format($boxes['total_boxes']) ?>
                </div>
                <div class="desc">
                    Toplam Koli
                </div>
            </div>
            <a class="more" href="<?= url('/boxes/list') ?>">
                Detayları Gör <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat purple">
            <div class="visual">
                <i class="fa fa-globe"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?= number_format($models['total']) ?>
                </div>
                <div class="desc">
                    Toplam Model
                </div>
            </div>
            <a class="more" href="<?= url('/orders/list') ?>">
                Detayları Gör <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="dashboard-stat yellow">
            <div class="visual">
                <i class="fa fa-check"></i>
            </div>
            <div class="details">
                <div class="number">
                    <?= number_format($orders['completed_orders']) ?>
                </div>
                <div class="desc">
                    Tamamlanan Sipariş
                </div>
            </div>
            <a class="more" href="<?= url('/reports?type=order&durum=completed') ?>">
                Detayları Gör <i class="m-icon-swapright m-icon-white"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Lot Durumu -->
    <div class="col-md-6">
        <div class="portlet solid bordered light-grey">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-bar-chart-o"></i>Lot Durumu
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="lot_chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    
    <!-- Ülke Dağılımı -->
    <div class="col-md-6">
        <div class="portlet solid bordered light-grey">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-globe"></i>Teslimat Ülkeleri
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div id="country_chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Son Eklenen Modeller -->
    <div class="col-md-12">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-gift"></i>Son Eklenen Modeller
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Model Kodu</th>
                                <th>Resim</th>
                                <th>Sipariş Sayısı</th>
                                <th>Oluşturulma Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentModels as $model): ?>
                                <tr>
                                    <td><?= $model['model_kodu'] ?></td>
                                    <td>
                                        <?php if ($model['image_path']): ?>
                                            <img src="<?= url($model['image_path']) ?>" alt="<?= $model['model_kodu'] ?>" width="50">
                                        <?php else: ?>
                                            <span class="label label-sm label-warning">Resim Yok</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $model['order_count'] ?></td>
                                    <td><?= format_date($model['created_at'], DATETIME_FORMAT) ?></td>
                                    <td>
                                        <a href="<?= url('/orders/view/' . $model['model_kodu']) ?>" class="btn btn-xs blue">
                                            <i class="fa fa-search"></i> Detaylar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentModels)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Henüz model bulunmuyor.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Grafik verileri hazırla
$lotData = [
    ['category' => 'Sipariş Edilen', 'value' => $orders['total_lots']],
    ['category' => 'Depo Girişi Olan', 'value' => $orders['total_deposited_lots']],
    ['category' => 'Kalan', 'value' => $orders['total_lots'] - $orders['total_deposited_lots']]
];

$countryData = [];
foreach ($orders['countries_distribution'] as $country) {
    $countryData[] = [
        'category' => $country['teslimat_ulkesi'],
        'value' => $country['count']
    ];
}

// Sayfa özel script
$pageScript = '
    // Lot Durumu Grafiği
    $("#lot_chart").kendoChart({
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
            data: [' . $orders['total_lots'] . ', ' . $orders['total_deposited_lots'] . ', ' . ($orders['total_lots'] - $orders['total_deposited_lots']) . '],
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
    $("#country_chart").kendoChart({
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
?>