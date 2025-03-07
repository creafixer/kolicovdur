<?php
// Veritabanı tablo yapıları
return [
    'models' => [
        'id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'model_kodu VARCHAR(50) NOT NULL',
        'image_path VARCHAR(255) NULL',
        'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        'UNIQUE KEY (model_kodu)'
    ],
    
    'orders' => [
        'id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'model_id INT(11) UNSIGNED NOT NULL',
        'sezon VARCHAR(20) NOT NULL',
        'siparis_numarasi VARCHAR(50) NOT NULL',
        'ship_to TEXT NOT NULL',
        'tedarikci_termini DATE NOT NULL',
        'renk_kodu_adi VARCHAR(100) NOT NULL',
        'lot_kodu VARCHAR(100) NOT NULL',
        'set_icerigi VARCHAR(50) NOT NULL',
        'bir_lottaki_urun_sayisi INT(11) NOT NULL',
        'teslimat_ulkesi VARCHAR(50) NOT NULL',
        'siparis_gecilen_lot_sayisi INT(11) NOT NULL',
        'siparis_gecilen_acik_adet_sayisi INT(11) NOT NULL',
        'depo_girisi_olan_lot_sayisi INT(11) NOT NULL DEFAULT 0',
        'depo_girisi_olan_acik_adet_sayisi INT(11) NOT NULL DEFAULT 0',
        'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        'FOREIGN KEY (model_id) REFERENCES models(id) ON DELETE CASCADE'
    ],
    
    'order_sizes' => [
        'id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'order_id INT(11) UNSIGNED NOT NULL',
        'beden_adi VARCHAR(20) NOT NULL',
        'adet INT(11) NOT NULL DEFAULT 0',
        'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        'FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE'
    ],
    
    'boxes' => [
        'id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'model_id INT(11) UNSIGNED NOT NULL',
        'siparis_id INT(11) UNSIGNED NOT NULL',
        'box_type ENUM("tam", "kırık") NOT NULL DEFAULT "tam"',
        'lot_count INT(11) NOT NULL',
        'box_number INT(11) NOT NULL',
        'status ENUM("hazırlanıyor", "hazır", "etiket_basıldı", "teslim_edildi") NOT NULL DEFAULT "hazırlanıyor"',
        'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        'FOREIGN KEY (model_id) REFERENCES models(id) ON DELETE CASCADE',
        'FOREIGN KEY (siparis_id) REFERENCES orders(id) ON DELETE CASCADE'
    ],
    
    'box_details' => [
        'id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'box_id INT(11) UNSIGNED NOT NULL',
        'beden_adi VARCHAR(20) NOT NULL',
        'adet INT(11) NOT NULL',
        'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        'FOREIGN KEY (box_id) REFERENCES boxes(id) ON DELETE CASCADE'
    ],
    
    'box_labels' => [
        'id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'box_id INT(11) UNSIGNED NOT NULL',
        'label_status ENUM("indirilmedi", "indirildi", "basıldı", "kayıp", "tekrar_basıldı") NOT NULL DEFAULT "indirilmedi"',
        'teslim_edilen_kisi VARCHAR(100) NULL',
        'teslim_tarihi DATETIME NULL',
        'teslim_adet INT(11) NULL',
        'notes TEXT NULL',
        'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        'FOREIGN KEY (box_id) REFERENCES boxes(id) ON DELETE CASCADE'
    ],
    
    'order_history' => [
        'id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY',
        'order_id INT(11) UNSIGNED NOT NULL',
        'action_type VARCHAR(50) NOT NULL',
        'old_data TEXT NULL',
        'new_data TEXT NULL',
        'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        'user VARCHAR(50) NULL',
        'FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE'
    ]
];