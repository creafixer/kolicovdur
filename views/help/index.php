<div class="row">
    <div class="col-md-12">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-question-circle font-green-sharp"></i>
                    <span class="caption-subject font-green-sharp bold uppercase">Yardım ve Kullanım Kılavuzu</span>
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a>
                    <a href="javascript:;" class="reload"></a>
                </div>
            </div>
            <div class="portlet-body">
                <div class="tabbable-custom ">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab_genel" data-toggle="tab">Genel Bilgiler</a>
                        </li>
                        <li>
                            <a href="#tab_veri_girisi" data-toggle="tab">Veri Girişi</a>
                        </li>
                        <li>
                            <a href="#tab_koli_hesaplama" data-toggle="tab">Koli Hesaplama</a>
                        </li>
                        <li>
                            <a href="#tab_raporlar" data-toggle="tab">Raporlar</a>
                        </li>
                        <li>
                            <a href="#tab_teknik" data-toggle="tab">Teknik Bilgiler</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_genel">
                            <h3>Koli Çovdur Uygulaması Hakkında</h3>
                            <p>
                                Koli Çovdur, Excel dosyalarından alınan sipariş verilerini işleyerek, veritabanına kaydeden, koli hesaplamaları yapan ve çeşitli raporlama işlevleri sunan kapsamlı bir sipariş takip uygulamasıdır.
                            </p>
                            
                            <h4>Ana Özellikler</h4>
                            <ul>
                                <li>Excel verilerini içe aktarma ve önizleme</li>
                                <li>Model bazlı sipariş listeleme ve detay görüntüleme</li>
                                <li>Koli hesaplama ve etiket yönetimi</li>
                                <li>Kapsamlı raporlama ve dışa aktarma seçenekleri</li>
                            </ul>
                            
                            <h4>Ana Menü</h4>
                            <ul>
                                <li><strong>Ana Sayfa:</strong> Genel istatistikler ve son eklenen modeller</li>
                                <li><strong>Siparişler:</strong> Veri içe aktarma ve model listesi</li>
                                <li><strong>Koli İşlemleri:</strong> Koli hesaplama ve koli listesi</li>
                                <li><strong>Raporlar:</strong> Çeşitli rapor tipleri ve filtreleme seçenekleri</li>
                                <li><strong>Yardım:</strong> Uygulama kullanımı hakkında bilgiler</li>
                            </ul>
                        </div>
                        <div class="tab-pane" id="tab_veri_girisi">
                            <h3>Excel Verilerini İçe Aktarma</h3>
                            <p>
                                Koli Çovdur uygulaması, Excel dosyalarından kopyalanan verileri işleyerek veritabanına kaydeder. Veriler iki ana tablo halinde düzenlenmiş olmalıdır: "Toplam Sipariş" ve "Beden Bazlı Toplam Sipariş".
                            </p>
                            
                            <h4>Veri İçe Aktarma Adımları</h4>
                            <ol>
                                <li>Sol menüden "Siparişler > Veri İçe Aktar" seçeneğine tıklayın.</li>
                                <li>Excel dosyanızı açın ve verileri (her iki tabloyu da içerecek şekilde) seçip kopyalayın (Ctrl+C).</li>
                                <li>Kopyalanan verileri sayfadaki metin alanına yapıştırın (Ctrl+V).</li>
                                <li>"Önizle" butonuna tıklayarak verilerin doğru şekilde ayrıştırıldığından emin olun.</li>
                                <li>Veriler doğruysa "Verileri Kaydet" butonuna tıklayarak veritabanına kaydedin.</li>
                            </ol>
                            
                            <h4>Excel Veri Formatı</h4>
                            <p>
                                Excel dosyasındaki veriler aşağıdaki formatta olmalıdır:
                            </p>
                            <ul>
                                <li>İlk satır "Toplam Sipariş" başlığını içermelidir.</li>
                                <li>İkinci satır sütun adlarını içermelidir (Model Kodu, Sezon, Sipariş Numarası, vb.).</li>
                                <li>Sonraki satırlar sipariş verilerini içermelidir.</li>
                                <li>Birkaç boş satırdan sonra "Beden Bazlı Toplam Sipariş" başlığı gelmelidir.</li>
                                <li>Ardından sütun adları ve veriler gelmelidir.</li>
                            </ul>
                            
                            <div class="alert alert-info">
                                <strong>İpucu:</strong> Verileri yapıştırdıktan sonra mutlaka önizleme yaparak doğru şekilde ayrıştırıldığından emin olun.
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_koli_hesaplama">
                            <h3>Koli Hesaplama</h3>
                            <p>
                                Koli Çovdur uygulaması, sipariş verilerine göre koli hesaplaması yaparak, tam ve kırık kolilerin planlanmasını sağlar.
                            </p>
                            
                            <h4>Koli Hesaplama Adımları</h4>
                            <ol>
                                <li>Sol menüden "Koli İşlemleri > Koli Hesaplama" seçeneğine tıklayın.</li>
                                <li>Model kodunu yazın veya listeden seçin.</li>
                                <li>Koli içine girecek lot sayısını belirleyin (varsayılan: 10).</li>
                                <li>Hesaplama tipini seçin:
                                    <ul>
                                        <li><strong>Genel Hesaplama:</strong> Tüm siparişler için hesaplama yapar.</li>
                                        <li><strong>Sipariş Numarasına Özel:</strong> Belirli bir sipariş numarası için hesaplama yapar.</li>
                                    </ul>
                                </li>
                                <li>"Hesapla" butonuna tıklayarak hesaplamayı başlatın.</li>
                                <li>Hesaplama sonuçlarını inceleyin:
                                    <ul>
                                        <li>Tam koliler yeşil, kırık koliler sarı renkte gösterilir.</li>
                                        <li>Her koli için lot sayısı ve ürün adedi gösterilir.</li>
                                    </ul>
                                </li>
                                <li>Hesaplamaları kaydetmek için "Hesaplamaları Kaydet" butonuna tıklayın.</li>
                            </ol>
                            
                            <h4>Koli Durumları</h4>
                            <ul>
                                <li><strong>Hazırlanıyor:</strong> Koli henüz hazırlanma aşamasında.</li>
                                <li><strong>Hazır:</strong> Koli hazırlandı, etiketi basılmaya hazır.</li>
                                <li><strong>Etiket Basıldı:</strong> Kolinin etiketi basıldı.</li>
                                <li><strong>Teslim Edildi:</strong> Koli teslim edildi.</li>
                            </ul>
                            
                            <h4>Etiket Durumları</h4>
                            <ul>
                                <li><strong>İndirilmedi:</strong> Etiket henüz indirilmedi.</li>
                                <li><strong>İndirildi:</strong> Etiket indirildi, basılmaya hazır.</li>
                                <li><strong>Basıldı:</strong> Etiket basıldı.</li>
                                <li><strong>Kayıp:</strong> Etiket kayboldu.</li>
                                <li><strong>Tekrar Basıldı:</strong> Etiket tekrar basıldı.</li>
                            </ul>
                        </div>
                        <div class="tab-pane" id="tab_raporlar">
                            <h3>Raporlar</h3>
                            <p>
                                Koli Çovdur uygulaması, çeşitli rapor tipleri ve filtreleme seçenekleri sunarak, verilerin analiz edilmesini sağlar.
                            </p>
                            
                            <h4>Rapor Tipleri</h4>
                            <ul>
                                <li><strong>Genel Rapor:</strong> Genel istatistikler ve grafikler.</li>
                                <li><strong>Model Raporu:</strong> Model bazlı sipariş ve koli bilgileri.</li>
                                <li><strong>Sipariş Raporu:</strong> Sipariş detayları ve durumları.</li>
                                <li><strong>Koli Raporu:</strong> Koli bilgileri ve durumları.</li>
                                <li><strong>Etiket Raporu:</strong> Etiket bilgileri ve durumları.</li>
                            </ul>
                            
                            <h4>Filtreleme Seçenekleri</h4>
                            <ul>
                                <li><strong>Model Kodu:</strong> Belirli bir model kodu için filtreleme.</li>
                                <li><strong>Sipariş Numarası:</strong> Belirli bir sipariş numarası için filtreleme.</li>
                                <li><strong>Teslimat Ülkesi:</strong> Belirli bir teslimat ülkesi için filtreleme.</li>
                                <li><strong>Tarih Aralığı:</strong> Belirli bir tarih aralığı için filtreleme.</li>
                                <li><strong>Durum:</strong> Belirli bir durum için filtreleme.</li>
                            </ul>
                            
                            <h4>Dışa Aktarma Seçenekleri</h4>
                            <ul>
                                <li><strong>Excel:</strong> Raporu Excel formatında dışa aktar.</li>
                                <li><strong>PDF:</strong> Raporu PDF formatında dışa aktar.</li>
                                <li><strong>CSV:</strong> Raporu CSV formatında dışa aktar.</li>
                            </ul>
                        </div>
                        <div class="tab-pane" id="tab_teknik">
                            <h3>Teknik Bilgiler</h3>
                            <p>
                                Koli Çovdur uygulaması, aşağıdaki teknolojiler kullanılarak geliştirilmiştir:
                            </p>
                            
                            <h4>Kullanılan Teknolojiler</h4>
                            <ul>
                                <li><strong>PHP:</strong> Sunucu tarafı programlama dili.</li>
                                <li><strong>MySQL:</strong> Veritabanı yönetim sistemi.</li>
                                <li><strong>JavaScript:</strong> İstemci tarafı programlama dili.</li>
                                <li><strong>jQuery:</strong> JavaScript kütüphanesi.</li>
                                <li><strong>AJAX:</strong> Asenkron JavaScript ve XML.</li>
                                <li><strong>Bootstrap:</strong> Responsive tasarım framework'ü.</li>
                                <li><strong>Kendo UI:</strong> JavaScript UI bileşenleri.</li>
                                <li><strong>Conquer:</strong> Admin panel teması.</li>
                            </ul>
                            
                            <h4>Veritabanı Yapısı</h4>
                            <ul>
                                <li><strong>models:</strong> Model bilgileri.</li>
                                <li><strong>orders:</strong> Sipariş bilgileri.</li>
                                <li><strong>order_sizes:</strong> Sipariş beden bilgileri.</li>
                                <li><strong>boxes:</strong> Koli bilgileri.</li>
                                <li><strong>box_details:</strong> Koli detay bilgileri.</li>
                                <li><strong>box_labels:</strong> Koli etiket bilgileri.</li>
                                <li><strong>order_history:</strong> Sipariş geçmişi.</li>
                            </ul>
                            
                            <h4>Dosya Yapısı</h4>
                            <ul>
                                <li><strong>assets/:</strong> CSS, JavaScript, resim ve eklenti dosyaları.</li>
                                <li><strong>config/:</strong> Konfigürasyon dosyaları.</li>
                                <li><strong>controllers/:</strong> Controller sınıfları.</li>
                                <li><strong>core/:</strong> Çekirdek sınıflar ve yardımcı fonksiyonlar.</li>
                                <li><strong>models/:</strong> Model sınıfları.</li>
                                <li><strong>views/:</strong> Görünüm dosyaları.</li>
                                <li><strong>uploads/:</strong> Yüklenen dosyalar.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>