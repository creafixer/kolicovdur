<div class="row">
    <div class="col-md-12 page-404">
        <div class="number">
            <?= $code ?? '404' ?>
        </div>
        <div class="details">
            <h3><?= $message ?? 'Sayfa bulunamadı.' ?></h3>
            <p>
                Üzgünüz, aradığınız sayfa bulunamadı veya erişim izniniz yok.<br/>
                <a href="<?= url('/') ?>">Ana sayfaya dön</a>
            </p>
        </div>
    </div>
</div>