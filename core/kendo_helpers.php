<?php
// Kendo UI için yardımcı fonksiyonlar

/**
 * Kendo UI Grid için JS kodunu oluşturur
 *
 * @param string $elementId HTML element ID'si
 * @param array $options Grid ayarları
 * @return string JavaScript kodu
 */
function kendoGrid($elementId, $options = []) {
    $defaultOptions = [
        'dataSource' => [
            'data' => [],
            'pageSize' => 10
        ],
        'height' => 550,
        'sortable' => true,
        'filterable' => true,
        'pageable' => [
            'refresh' => true,
            'pageSizes' => [5, 10, 20, 50, 100],
            'buttonCount' => 5
        ],
        'columns' => []
    ];

    // Varsayılan ayarları kullanıcı ayarları ile birleştir
    $options = array_merge_recursive($defaultOptions, $options);

    // JSON formatına dönüştür
    $optionsJson = json_encode($options);

    // JavaScript kodunu oluştur
    $js = "
    <script>
        $(document).ready(function() {
            $('#{$elementId}').kendoGrid({$optionsJson});
        });
    </script>";

    return $js;
}

/**
 * Kendo UI DatePicker için JS kodunu oluşturur
 *
 * @param string $elementId HTML element ID'si
 * @param array $options DatePicker ayarları
 * @return string JavaScript kodu
 */
function kendoDatePicker($elementId, $options = []) {
    $defaultOptions = [
        'format' => 'dd.MM.yyyy',
        'culture' => KENDO_CULTURE
    ];

    // Varsayılan ayarları kullanıcı ayarları ile birleştir
    $options = array_merge($defaultOptions, $options);

    // JSON formatına dönüştür
    $optionsJson = json_encode($options);

    // JavaScript kodunu oluştur
    $js = "
    <script>
        $(document).ready(function() {
            $('#{$elementId}').kendoDatePicker({$optionsJson});
        });
    </script>";

    return $js;
}

/**
 * Kendo UI DropDownList için JS kodunu oluşturur
 *
 * @param string $elementId HTML element ID'si
 * @param array $options DropDownList ayarları
 * @return string JavaScript kodu
 */
function kendoDropDownList($elementId, $options = []) {
    $defaultOptions = [
        'dataTextField' => 'text',
        'dataValueField' => 'value',
        'dataSource' => [],
        'optionLabel' => 'Seçiniz...'
    ];

    // Varsayılan ayarları kullanıcı ayarları ile birleştir
    $options = array_merge($defaultOptions, $options);

    // JSON formatına dönüştür
    $optionsJson = json_encode($options);

    // JavaScript kodunu oluştur
    $js = "
    <script>
        $(document).ready(function() {
            $('#{$elementId}').kendoDropDownList({$optionsJson});
        });
    </script>";

    return $js;
}

/**
 * Kendo UI Upload için JS kodunu oluşturur
 *
 * @param string $elementId HTML element ID'si
 * @param array $options Upload ayarları
 * @return string JavaScript kodu
 */
function kendoUpload($elementId, $options = []) {
    $defaultOptions = [
        'async' => [
            'saveUrl' => SITE_URL . '/ajax/upload',
            'removeUrl' => SITE_URL . '/ajax/remove-upload',
            'autoUpload' => true
        ],
        'multiple' => false,
        'localization' => [
            'select' => 'Dosya Seç',
            'dropFilesHere' => 'Dosyaları buraya sürükleyin',
            'uploadSelectedFiles' => 'Dosyaları Yükle',
            'cancel' => 'İptal',
            'remove' => 'Kaldır'
        ]
    ];

    // Varsayılan ayarları kullanıcı ayarları ile birleştir
    $options = array_merge_recursive($defaultOptions, $options);

    // JSON formatına dönüştür
    $optionsJson = json_encode($options);

    // JavaScript kodunu oluştur
    $js = "
    <script>
        $(document).ready(function() {
            $('#{$elementId}').kendoUpload({$optionsJson});
        });
    </script>";

    return $js;
}

/**
 * Kendo UI Chart için JS kodunu oluşturur
 *
 * @param string $elementId HTML element ID'si
 * @param array $options Chart ayarları
 * @return string JavaScript kodu
 */
function kendoChart($elementId, $options = []) {
    $defaultOptions = [
        'title' => [
            'text' => 'Grafik'
        ],
        'legend' => [
            'position' => 'bottom'
        ],
        'seriesDefaults' => [
            'type' => 'column'
        ],
        'series' => [],
        'valueAxis' => [
            'labels' => [
                'format' => '{0}'
            ]
        ],
        'tooltip' => [
            'visible' => true,
            'format' => '{0}'
        ]
    ];

    // Varsayılan ayarları kullanıcı ayarları ile birleştir
    $options = array_merge_recursive($defaultOptions, $options);

    // JSON formatına dönüştür
    $optionsJson = json_encode($options);

    // JavaScript kodunu oluştur
    $js = "
    <script>
        $(document).ready(function() {
            $('#{$elementId}').kendoChart({$optionsJson});
        });
    </script>";

    return $js;
}

/**
 * Kendo UI Window için JS kodunu oluşturur
 *
 * @param string $elementId HTML element ID'si
 * @param array $options Window ayarları
 * @return string JavaScript kodu
 */
function kendoWindow($elementId, $options = []) {
    $defaultOptions = [
        'width' => '600px',
        'title' => 'Pencere',
        'visible' => false,
        'modal' => true,
        'actions' => ['Maximize', 'Close']
    ];

    // Varsayılan ayarları kullanıcı ayarları ile birleştir
    $options = array_merge($defaultOptions, $options);

    // JSON formatına dönüştür
    $optionsJson = json_encode($options);

    // JavaScript kodunu oluştur
    $js = "
    <script>
        $(document).ready(function() {
            $('#{$elementId}').kendoWindow({$optionsJson});
        });
    </script>";

    return $js;
}

/**
 * Kendo UI AutoComplete için JS kodunu oluşturur
 *
 * @param string $elementId HTML element ID'si
 * @param array $options AutoComplete ayarları
 * @return string JavaScript kodu
 */
function kendoAutoComplete($elementId, $options = []) {
    $defaultOptions = [
        'dataTextField' => 'text',
        'filter' => 'startswith',
        'minLength' => 3,
        'dataSource' => []
    ];

    // Varsayılan ayarları kullanıcı ayarları ile birleştir
    $options = array_merge($defaultOptions, $options);

    // JSON formatına dönüştür
    $optionsJson = json_encode($options);

    // JavaScript kodunu oluştur
    $js = "
    <script>
        $(document).ready(function() {
            $('#{$elementId}').kendoAutoComplete({$optionsJson});
        });
    </script>";

    return $js;
}

/**
 * Kendo UI TabStrip için JS kodunu oluşturur
 *
 * @param string $elementId HTML element ID'si
 * @param array $options TabStrip ayarları
 * @return string JavaScript kodu
 */
function kendoTabStrip($elementId, $options = []) {
    $defaultOptions = [
        'animation' => [
            'open' => [
                'effects' => 'fadeIn'
            ]
        ]
    ];

    // Varsayılan ayarları kullanıcı ayarları ile birleştir
    $options = array_merge($defaultOptions, $options);

    // JSON formatına dönüştür
    $optionsJson = json_encode($options);

    // JavaScript kodunu oluştur
    $js = "
    <script>
        $(document).ready(function() {
            $('#{$elementId}').kendoTabStrip({$optionsJson});
        });
    </script>";

    return $js;
}

/**
 * Kendo UI Notification için JS kodunu oluşturur
 *
 * @param string $elementId HTML element ID'si
 * @param array $options Notification ayarları
 * @return string JavaScript kodu
 */
function kendoNotification($elementId, $options = []) {
    $defaultOptions = [
        'position' => [
            'pinned' => true,
            'top' => 30,
            'right' => 30
        ],
        'autoHideAfter' => 5000,
        'stacking' => 'down',
        'templates' => [
            [
                'type' => 'success',
                'template' => '<div class="k-notification-success"><span class="k-icon k-i-success"></span><h3>#= title #</h3><p>#= message #</p></div>'
            ],
            [
                'type' => 'error',
                'template' => '<div class="k-notification-error"><span class="k-icon k-i-error"></span><h3>#= title #</h3><p>#= message #</p></div>'
            ],
            [
                'type' => 'warning',
                'template' => '<div class="k-notification-warning"><span class="k-icon k-i-warning"></span><h3>#= title #</h3><p>#= message #</p></div>'
            ],
            [
                'type' => 'info',
                'template' => '<div class="k-notification-info"><span class="k-icon k-i-info"></span><h3>#= title #</h3><p>#= message #</p></div>'
            ]
        ]
    ];

    // Varsayılan ayarları kullanıcı ayarları ile birleştir
    $options = array_merge_recursive($defaultOptions, $options);

    // JSON formatına dönüştür
    $optionsJson = json_encode($options);

    // JavaScript kodunu oluştur
    $js = "
    <script>
        $(document).ready(function() {
            var notification = $('#{$elementId}').kendoNotification({$optionsJson}).data('kendoNotification');
            
            // Global notification fonksiyonlarını tanımla
            window.showSuccess = function(title, message) {
                notification.show({
                    title: title,
                    message: message
                }, 'success');
            };
            
            window.showError = function(title, message) {
                notification.show({
                    title: title,
                    message: message
                }, 'error');
            };
            
            window.showWarning = function(title, message) {
                notification.show({
                    title: title,
                    message: message
                }, 'warning');
            };
            
            window.showInfo = function(title, message) {
                notification.show({
                    title: title,
                    message: message
                }, 'info');
            };
        });
    </script>";

    return $js;
}