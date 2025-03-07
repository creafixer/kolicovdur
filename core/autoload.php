<?php
/**
 * Otomatik sınıf yükleme
 */
spl_autoload_register(function($className) {
    // Controller sınıfları
    if (strpos($className, 'Controller') !== false) {
        $file = CONTROLLER_DIR . '/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    // Model sınıfları
    if (strpos($className, 'Model') !== false) {
        $file = MODEL_DIR . '/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    
    return false;
});