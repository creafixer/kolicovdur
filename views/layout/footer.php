                <!-- PAGE CONTENT END -->
            </div>
        </div>
    </div>
    
    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-inner">
            <?= date('Y') ?> &copy; <?= APP_NAME ?> v<?= APP_VERSION ?>
        </div>
        <div class="footer-tools">
            <span class="go-top">
                <i class="fa fa-angle-up"></i>
            </span>
        </div>
    </div>
    
    <!-- SCRIPTS -->

    <!-- BEGIN CORE PLUGINS -->
<script src="<?= THEME_URL ?>/assets/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
<script src="<?= THEME_URL ?>/assets/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="<?= THEME_URL ?>/assets/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="<?= THEME_URL ?>/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?= THEME_URL ?>/assets/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="<?= THEME_URL ?>/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?= THEME_URL ?>/assets/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?= THEME_URL ?>/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
        <!-- Kendo UI -->
        <script src="<?= SITE_URL ?>/assets/plugins/kendo/js/kendo.all.min.js"></script>
        <script src="<?= SITE_URL ?>/assets/plugins/kendo/js/cultures/kendo.culture.tr-TR.min.js"></script>
        
        <script>
            // Kendo UI Türkçe dil ayarı
            kendo.culture("tr-TR");
        </script>
        
<script src="<?= THEME_URL ?>/assets/scripts/app.js"></script>
<script>
jQuery(document).ready(function() {    
   App.init();
            // Sayfa özel script kodları
            <?php if (isset($pageScript)): ?>
                <?= $pageScript ?>
            <?php endif; ?>
        });
</script>
<!-- END JAVASCRIPTS -->
     

</body>
</html>