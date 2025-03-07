/**
 * Koli Çovdur Özel JavaScript Dosyası
 */

// Döküman hazır olduğunda
$(document).ready(function() {
    // Tooltip'leri etkinleştir
    $('[data-toggle="tooltip"]').tooltip();
    
    // Popover'ları etkinleştir
    $('[data-toggle="popover"]').popover();
    
    // Tarih alanlarını formatla
    $('.date-picker').each(function() {
        $(this).datepicker({
            rtl: false,
            orientation: "left",
            autoclose: true,
            format: 'dd.mm.yyyy',
            language: 'tr'
        });
    });
    
    // Mobil menü düğmesi
    $('.navbar-toggle').on('click', function() {
        if ($('.page-sidebar').hasClass('in')) {
            $('.page-sidebar').removeClass('in');
        } else {
            $('.page-sidebar').addClass('in');
        }
    });
    
    // Sayfa üstüne çık düğmesi
    $('.go-top').on('click', function(e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });
    
    // Akordeon etkisi
    $('.accordion-toggle').on('click', function() {
        $(this).toggleClass('collapsed');
    });
    
    // Tablo sıralama
    $('table.sortable').each(function() {
        var $table = $(this);
        
        $table.find('th').each(function() {
            var $th = $(this);
            var $thIndex = $th.index();
            
            if (!$th.hasClass('no-sort')) {
                $th.addClass('sorting').on('click', function() {
                    var rows = $table.find('tbody > tr').get();
                    var isAsc = $th.hasClass('sorting_asc');
                    
                    $table.find('th').removeClass('sorting_asc sorting_desc');
                    
                    if (isAsc) {
                        $th.addClass('sorting_desc');
                    } else {
                        $th.addClass('sorting_asc');
                    }
                    
                    rows.sort(function(a, b) {
                        var A = $(a).children('td').eq($thIndex).text().toUpperCase();
                        var B = $(b).children('td').eq($thIndex).text().toUpperCase();
                        
                        if (!isNaN(A) && !isNaN(B)) {
                            A = parseFloat(A);
                            B = parseFloat(B);
                        }
                        
                        return isAsc ? (A > B ? -1 : A < B ? 1 : 0) : (A < B ? -1 : A > B ? 1 : 0);
                    });
                    
                    $.each(rows, function(index, row) {
                        $table.children('tbody').append(row);
                    });
                    
                    return false;
                });
            }
        });
    });
    
    // Arama kutusu
    $('.search-input').on('keyup', function() {
        var searchText = $(this).val().toLowerCase();
        var targetTable = $($(this).data('target'));
        
        targetTable.find('tbody tr').each(function() {
            var rowText = $(this).text().toLowerCase();
            
            if (rowText.indexOf(searchText) === -1) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    });
    
    // Tablo satırı seçme
    $('table.selectable tbody').on('click', 'tr', function() {
        $(this).toggleClass('selected');
    });
    
    // Tüm satırları seç/kaldır
    $('table.selectable thead .select-all').on('change', function() {
        var isChecked = $(this).prop('checked');
        var targetTable = $(this).closest('table');
        
        targetTable.find('tbody input[type="checkbox"]').prop('checked', isChecked);
        
        if (isChecked) {
            targetTable.find('tbody tr').addClass('selected');
        } else {
            targetTable.find('tbody tr').removeClass('selected');
        }
    });
    
    // Yükleme işlemi gösterici
    $(document).ajaxStart(function() {
        showLoading();
    }).ajaxStop(function() {
        hideLoading();
    });
    
    // Oturum zaman aşımı kontrolü
    var sessionTimeout = 30 * 60 * 1000; // 30 dakika
    var sessionTimer;
    
    function resetSessionTimer() {
        clearTimeout(sessionTimer);
        sessionTimer = setTimeout(function() {
            showSessionTimeoutWarning();
        }, sessionTimeout);
    }
    
    function showSessionTimeoutWarning() {
        // Oturum zaman aşımı uyarısı
        alert('Oturumunuz zaman aşımına uğramak üzere. Lütfen sayfayı yenileyin.');
    }
    
    // Kullanıcı etkileşimlerinde zamanlayıcıyı sıfırla
    $(document).on('click keypress', resetSessionTimer);
    resetSessionTimer();
});

/**
 * Yükleme göstergesi
 */
function showLoading() {
    if ($('#globalLoading').length === 0) {
        $('body').append('<div id="globalLoading" class="loading-overlay"><div class="loading-spinner"></div></div>');
    }
    
    $('#globalLoading').fadeIn(200);
}

/**
 * Yükleme göstergesini gizle
 */
function hideLoading() {
    $('#globalLoading').fadeOut(200);
}

/**
 * Bildirim göster
 * 
 * @param {string} message Bildirim mesajı
 * @param {string} type Bildirim tipi (success, error, info, warning)
 * @param {number} timeout Kapanma süresi (ms)
 */
function showNotification(message, type, timeout) {
    type = type || 'info';
    timeout = timeout || 5000;
    
    var iconClass = '';
    
    switch (type) {
        case 'success':
            iconClass = 'fa fa-check-circle';
            break;
        case 'error':
            iconClass = 'fa fa-times-circle';
            break;
        case 'warning':
            iconClass = 'fa fa-exclamation-triangle';
            break;
        case 'info':
        default:
            iconClass = 'fa fa-info-circle';
            break;
    }
    
    var notification = $('<div class="custom-notification ' + type + '"><i class="' + iconClass + '"></i>' + message + '</div>');
    
    $('#notificationContainer').length ? '' : $('body').append('<div id="notificationContainer"></div>');
    
    $('#notificationContainer').append(notification);
    
    notification.animate({
        opacity: 1,
        right: '10px'
    }, 500);
    
    setTimeout(function() {
        notification.animate({
            opacity: 0,
            right: '-300px'
        }, 500, function() {
            notification.remove();
        });
    }, timeout);
}

/**
 * Onay kutusu göster
 * 
 * @param {string} message Onay mesajı
 * @param {function} callback Onay fonksiyonu
 */
function showConfirmation(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

/**
 * Tarih formatla
 * 
 * @param {Date|string} date Tarih nesnesi veya string
 * @param {string} format Format (default: 'dd.MM.yyyy')
 * @return {string} Formatlanmış tarih
 */
function formatDate(date, format) {
    if (!date) return '';
    
    format = format || 'dd.MM.yyyy';
    
    if (typeof date === 'string') {
        date = new Date(date);
    }
    
    var day = date.getDate().toString().padStart(2, '0');
    var month = (date.getMonth() + 1).toString().padStart(2, '0');
    var year = date.getFullYear();
    var hours = date.getHours().toString().padStart(2, '0');
    var minutes = date.getMinutes().toString().padStart(2, '0');
    var seconds = date.getSeconds().toString().padStart(2, '0');
    
    format = format.replace('dd', day);
    format = format.replace('MM', month);
    format = format.replace('yyyy', year);
    format = format.replace('HH', hours);
    format = format.replace('mm', minutes);
    format = format.replace('ss', seconds);
    
    return format;
}

/**
 * Sayıyı para birimi formatına dönüştür
 * 
 * @param {number} number Sayı
 * @param {number} decimals Ondalık basamak sayısı
 * @param {string} decimalSeparator Ondalık ayırıcı
 * @param {string} thousandsSeparator Binlik ayırıcı
 * @return {string} Formatlanmış sayı
 */
function formatNumber(number, decimals, decimalSeparator, thousandsSeparator) {
    decimals = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
    decimalSeparator = decimalSeparator === undefined ? ',' : decimalSeparator;
    thousandsSeparator = thousandsSeparator === undefined ? '.' : thousandsSeparator;
    
    var number = parseFloat(number);
    
    if (isNaN(number)) return '0';
    
    var negative = number < 0 ? '-' : '';
    var i = parseInt(number = Math.abs(+number || 0).toFixed(decimals)) + '';
    var j = (j = i.length) > 3 ? j % 3 : 0;
    
    return negative + 
        (j ? i.substr(0, j) + thousandsSeparator : '') + 
        i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousandsSeparator) + 
        (decimals ? decimalSeparator + Math.abs(number - i).toFixed(decimals).slice(2) : '');
}
/**
 * Özel JavaScript Fonksiyonları
 */
$(document).ready(function() {
    // Kendo UI Grid'leri yapılandır
    if ($.fn.kendoGrid) {
        $(".kendo-grid").each(function() {
            var gridOptions = $(this).data("grid-options") || {};
            $(this).kendoGrid(gridOptions);
        });
    }
    
    // Kendo UI DatePicker'ları yapılandır
    if ($.fn.kendoDatePicker) {
        $(".kendo-date").kendoDatePicker({
            format: "dd.MM.yyyy",
            culture: "tr-TR"
        });
    }
    
    // Kendo UI DropDownList'leri yapılandır
    if ($.fn.kendoDropDownList) {
        $(".kendo-dropdown").kendoDropDownList();
    }
    
    // Kendo UI TabStrip'leri yapılandır
    if ($.fn.kendoTabStrip) {
        $(".kendo-tabstrip").kendoTabStrip({
            animation: {
                open: {
                    effects: "fadeIn"
                }
            }
        });
    }
    
    // Ajax form gönderimi
    $(".ajax-form").on("submit", function(e) {
        e.preventDefault();
        
        var form = $(this);
        var url = form.attr("action");
        var method = form.attr("method") || "POST";
        var data = form.serialize();
        
        $.ajax({
            url: url,
            type: method,
            data: data,
            success: function(response) {
                if (response.success) {
                    if (response.message) {
                        alert(response.message);
                    }
                    
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                } else {
                    alert(response.error || "İşlem sırasında bir hata oluştu!");
                }
            },
            error: function() {
                alert("Sunucu ile iletişim kurulamadı!");
            }
        });
    });
});