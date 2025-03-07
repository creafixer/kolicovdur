<!-- Sidebar Başlangıç -->
<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">
        <ul class="page-sidebar-menu" data-auto-scroll="true" data-slide-speed="200">
            <li class="sidebar-toggler-wrapper">
                <div class="sidebar-toggler"></div>
            </li>
            <div class="clearfix">
            </div>

            
            <li class="start <?= ($activePage == 'dashboard') ? 'active' : '' ?>">
                <a href="<?= SITE_URL ?>">
                    <i class="fa fa-home"></i> 
                    <span class="title">Ana Sayfa</span>
                </a>
            </li>
            
            <li class="<?= (strpos($activePage, 'orders') !== false) ? 'active' : '' ?>">
                <a href="javascript:;">
                    <i class="fa fa-shopping-cart"></i> 
                    <span class="title">Siparişler</span>
                    <span class="arrow <?= (strpos($activePage, 'orders') !== false) ? 'open' : '' ?>"></span>
                </a>
                <ul class="sub-menu">
                    <li class="<?= ($activePage == 'orders-import') ? 'active' : '' ?>">
                        <a href="<?= SITE_URL ?>/orders/import">
                            <i class="fa fa-upload"></i> Veri İçe Aktar
                        </a>
                    </li>
                    <li class="<?= ($activePage == 'orders-list') ? 'active' : '' ?>">
                        <a href="<?= SITE_URL ?>/orders/list">
                            <i class="fa fa-list"></i> Model Listesi
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="<?= (strpos($activePage, 'boxes') !== false) ? 'active' : '' ?>">
                <a href="javascript:;">
                    <i class="fa fa-cube"></i> 
                    <span class="title">Koli İşlemleri</span>
                    <span class="arrow <?= (strpos($activePage, 'boxes') !== false) ? 'open' : '' ?>"></span>
                </a>
                <ul class="sub-menu">
                    <li class="<?= ($activePage == 'boxes-calculate') ? 'active' : '' ?>">
                        <a href="<?= SITE_URL ?>/boxes/calculate">
                            <i class="fa fa-calculator"></i> Koli Hesaplama
                        </a>
                    </li>
                    <li class="<?= ($activePage == 'boxes-list') ? 'active' : '' ?>">
                        <a href="<?= SITE_URL ?>/boxes/list">
                            <i class="fa fa-cubes"></i> Koli Listesi
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="<?= ($activePage == 'reports') ? 'active' : '' ?>">
                <a href="<?= SITE_URL ?>/reports">
                    <i class="fa fa-bar-chart-o"></i> 
                    <span class="title">Raporlar</span>
                </a>
            </li>
            
            <li class="<?= ($activePage == 'help') ? 'active' : '' ?>">
                <a href="<?= SITE_URL ?>/help">
                    <i class="fa fa-question-circle"></i> 
                    <span class="title">Yardım</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- Sidebar Bitiş -->