<?php
/* =====================================================================
   BBMTank — OYUN REHBERI  (24.08.2026)
   İçerik uydurma değil: mekanikler oyunun kendi kodundan doğrulandı
   (güç barı 0..2000 / 498px, M tuşu işaret dolumu, keşif = tek boss,
   tezgah kurma kuralları, otomatik av enerji/kilit kuralları).
   ===================================================================== */
ini_set('default_charset', 'UTF-8');
$sayfaAd       = 'rehber';
$sayfaBaslik   = 'Oyun Rehberi';
$sayfaAciklama = 'BBMTank oyun rehberi: açı ve güç ayarı, rüzgâr hesabı, M tuşu ile otomatik güç, keşifler, Pazar Alanı, Otomatik Av ve karakter gelişimi.';
$sayfaAnahtar  = 'bbmtank rehber, tank oyunu nasıl oynanır, güç barı, m tuşu, keşif, pazar alani, otomatik av';
include('parts/ust.php');
?>

<!-- ================= GIRIS ================= -->
<section class="bolum" id="basla">
  <div class="wrap">
    <div class="baslik">
      <div class="rv">
        <span class="tepe">Rehber</span>
        <h1 class="vurgu">Ateş etmeyi<br>bilmek yetmez</h1>
        <p class="aciklama">
          BBMTank sıra tabanlı bir topçu oyunu. Kazanan, en çok tıklayan değil
          açıyı, gücü ve rüzgârı birlikte hesaplayan oyuncu. Bu rehber oyunun
          gerçek mekaniklerini adım adım anlatır.
        </p>
      </div>
      <div class="rv" style="display:flex;gap:44px;flex-wrap:wrap;justify-content:center;margin-top:28px">
        <div class="sayac"><b data-say="16">0</b><span>Keşif Odası</span></div>
        <div class="sayac"><b data-say="5">0</b><span>Zorluk Kademesi</span></div>
        <div class="sayac"><b data-say="80">0</b><span>Keşif Görevi</span></div>
      </div>
    </div>

    <div class="izgara i4">
      <div class="levha rv">
        <div class="flama altin">01</div><h3>Kayıt ol</h3>
        <p>Ücretsiz hesap aç, karakterini seç. Kurulum yok, oyun tarayıcıda çalışır.</p>
      </div>
      <div class="levha rv">
        <div class="flama altin">02</div><h3>Eğitimi bitir</h3>
        <p>İlk 15 seviye başlangıç aşamasıdır; savaşta otomatik rehber devrededir.</p>
      </div>
      <div class="levha rv">
        <div class="flama altin">03</div><h3>Keşfe gir</h3>
        <p>Keşif Rıhtımı'ndan oda kur, seviyene uygun bossu seç ve ödülleri topla.</p>
      </div>
      <div class="levha rv">
        <div class="flama altin">04</div><h3>Geliştir</h3>
        <p>Düşeni sat, güçlendir, efsun yap. Pazar Alanı'nda kendi tezgahını kur.</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= SAVAS MEKANIKLERI ================= -->
<section class="bolum" id="savas">
  <div class="wrap">
    <div class="baslik">
      <div class="rv">
        <span class="tepe">Mekanik</span>
        <h2>Savaşın <span class="vurgu">üç değişkeni</span></h2>
        <p class="aciklama">
          Her atışı belirleyen üç şey var: açı, güç ve rüzgâr. Üçünü birlikte
          okuyamayan oyuncu, ekipmanı ne kadar iyi olursa olsun isabet ettiremez.
        </p>
      </div>
    </div>

    <div class="izgara i3">
      <div class="levha tiklanir rv" data-modal
           data-baslik="Açı"
           data-üst='<span class="rozet">Sol alt kadran</span>'
           data-govde="<p>Ekranın sol alt köşesindeki kadran namlunun açısını gösterir. Yukarı ok ve aşağı ok tuşlarıyla derece derece ayarlanır.</p><p><b>İpucu:</b> Yüksek açı mermiyi daha tepeden düşürür; arada engel varsa tek çözüm budur. Alçak açı daha hızlı ve düz gider, rüzgârdan daha az etkilenir.</p>">
        <div class="flama altin">AÇI</div>
        <h3>Kadran</h3>
        <p>Namlunun yükselişi. Engel varsa yüksek açı, düz hatta alçak açı. Yukarı ve aşağı ok tuşlarıyla ayarlanır.</p>
        <span class="rozet" style="margin-top:14px">Detay için tıkla</span>
      </div>

      <div class="levha tiklanir rv" data-modal
           data-baslik="Güç"
           data-üst='<span class="rozet mavi">Alt şeritteki 0 - 100 barı</span>'
           data-govde="<p>Ekranın altındaki uzun şerit gücü gösterir. <kbd>BOŞLUK</kbd> tuşunu basılı tuttuğun sürece dolar, bıraktığında mermi gider. Bar sonuna gelirse geri döner; yani sonsuza kadar tutmak işe yaramaz.</p><p><b>Önemli:</b> Bar üzerine fare ile tıklarsan o noktaya bir <b>işaret</b> koyarsın. Sonra <kbd>M</kbd> tuşuna basmak yeterli: güç kendiliğinden işarete kadar dolar ve atış yapılır. Aynı güçle art arda atmak için en hızlı yol budur, işaret yeni turda da durur.</p>">
        <div class="flama altin">GÜÇ</div>
        <h3>Bar ve <kbd>M</kbd></h3>
        <p><kbd>BOŞLUK</kbd> basılı tut, bar dolsun, bırak. Ya da bara tıklayıp işaret koy, <kbd>M</kbd> ile tam o güce kadar otomatik doldur.</p>
        <span class="rozet mavi" style="margin-top:14px">Detay için tıkla</span>
      </div>

      <div class="levha tiklanir rv" data-modal
           data-baslik="Rüzgâr"
           data-üst='<span class="rozet mavi">Ekranın üst ortası</span>'
           data-govde="<p>Üst ortadaki ok rüzgârın yönünü, yanındaki sayı ise şiddetini gösterir. Rüzgâr merminin yatay yolunu büker.</p><p><b>Nasıl hesaplanır:</b> Rüzgâr atış yönünle aynı tarafa esiyorsa mermi daha uzağa düşer, gücü bir tık azalt. Ters esiyorsa menzil kısalır, gücü artır. Şiddet büyüdükçe düzeltme de büyür.</p><p>Rüzgâr her turda değişir; önceki turda tutan ayar bu turda tutmayabilir.</p>">
        <div class="flama altin">RÜZGÂR</div>
        <h3>Yön ve şiddet</h3>
        <p>Her tur değişir. Arkadan eserse menzil uzar, önden eserse kısalır. Gücü ona göre düzelt.</p>
        <span class="rozet mavi" style="margin-top:14px">Detay için tıkla</span>
      </div>
    </div>

    <div class="izgara i2" style="margin-top:18px">
      <div class="levha rv">
        <h3>Tur süresi ve pas</h3>
        <p style="margin-top:8px">
          Sıran geldiğinde üstte bir geri sayım başlar. Süre biterse tur otomatik geçer.
          Atış yapmadan geçmek istersen <kbd>P</kbd> tuşu turu pas geçer.
        </p>
      </div>
      <div class="levha rv">
        <h3>Aksesuar tuşları</h3>
        <p style="margin-top:8px">
          Alt şeritteki <kbd>Z</kbd>, <kbd>X</kbd> ve <kbd>C</kbd> yuvalarına savaş
          aksesuarlarını koyarsın. Can ilacı, güç artırıcı ve benzeri eşyalar
          tur içinde bu tuşlarla kullanılır.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ================= KESIFLER ================= -->
<section class="bolum" id="kesif">
  <div class="wrap">
    <div class="baslik">
      <div class="rv">
        <span class="tepe">İçerik</span>
        <h2>Keşifler: <span class="vurgu">tek boss, net dövüş</span></h2>
        <p class="aciklama">
          BBMTank keşifleri kat kat ilerleyen zindanlar değil. Odaya giriyorsun,
          bossu karşında buluyorsun, indirdiğinde keşif bitiyor. Her keşfin kendi
          bossu, kendi haritası ve kendi drop tablosu var.
        </p>
      </div>
      <div class="rv">
        <a class="btn" href="kesifler.php">Tüm keşifleri gör</a>
      </div>
    </div>

    <div class="izgara i3">
      <div class="levha rv">
        <h3>Beş zorluk kademesi</h3>
        <p style="margin-top:8px">
          Her keşif <b>Kolay, Normal, Zor, Dehşet ve Epik</b> olarak oynanır.
          Kademe yükseldikçe bossun canı ve ödülün değeri birlikte artar.
        </p>
      </div>
      <div class="levha rv">
        <h3>Kart açma</h3>
        <p style="margin-top:8px">
          Keşfi kazandığında kart açarsın. Açılan her kart o keşfin drop
          tablosundan bağımsız bir çekiliş yapar; ödül doğrudan çantana düşer.
        </p>
      </div>
      <div class="levha rv">
        <h3>Enerji</h3>
        <p style="margin-top:8px">
          Kazanılan her keşif enerji harcar. Enerjin bittiğinde kart açsan bile
          ödül düşmez. Enerji her gün yenilenir, ayrıca satın alınabilir.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ================= PAZAR ================= -->
<section class="bolum" id="pazar">
  <div class="wrap">
    <div class="baslik">
      <div class="rv">
        <span class="tepe">Ekonomi</span>
        <h2>Pazar Alanı: <span class="vurgu">kendi tezgahin</span></h2>
        <p class="aciklama">
          Pazar Alanı yürünebilir bir meydan. Boş bir tezgah noktasına geçip
          kendi dükkanını açar, çantandaki bağlı olmayan eşyaları fiyat koyup
          dizersin. Diger oyuncular gezip senden satın alır.
        </p>
      </div>
    </div>

    <div class="izgara i4">
      <div class="levha rv"><div class="flama altin">01</div><h3>Noktaya geç</h3>
        <p>Tezgah yalnızca işaretli tezgah alanlarına kurulur. Alanın üzerinde bir an hareketsiz bekle.</p></div>
      <div class="levha rv"><div class="flama altin">02</div><h3>Tezgah kur</h3>
        <p>Adını yaz, tezgah ve etiket türünü seç, süresini belirle: 6, 12, 24 saat ya da 2 gün.</p></div>
      <div class="levha rv"><div class="flama altin">03</div><h3>Eşyayı diz</h3>
        <p>Çantadan eşyayı slota sürükle, adet ve fiyat ver. Bağlı eşyalar tezgaha konamaz.</p></div>
      <div class="levha rv"><div class="flama altin">04</div><h3>Süre dolunca</h3>
        <p>Tezgah kapanır, satılmayan eşyalar postayla sana geri döner. Kaybolmaz.</p></div>
    </div>

    <div class="levha rv" style="margin-top:18px">
      <h3>Bilinmesi gerekenler</h3>
      <div class="izgara i2" style="margin-top:14px">
        <p style="margin:0">
          Aynı anda <b>bir tezgah</b> açabilirsin. VIP oyuncular daha fazlasını
          acabilir: VIP 6 ve üstü iki, VIP 12 ve üstü üç tezgah.
        </p>
        <p style="margin:0">
          Baska bir tezgaha çok yakın bir noktaya kuramazsın. Tezgahın bitişine
          30 dakikadan az kaldıysa yeni ürün eklenmez.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ================= OTOMATIK AV ================= -->
<section class="bolum" id="otoav">
  <div class="wrap">
    <div class="baslik">
      <div class="rv">
        <span class="tepe">Yeni</span>
        <h2>Otomatik Av: <span class="vurgu">uyurken kasmak</span></h2>
        <p class="aciklama">
          Bir keşfi yeterince öğrendiysen artık onu tek tek oynamana gerek yok.
          Otomatik Av, o keşfi senin adına tekrar tekrar koşar ve ödülleri biriktirir.
          Festival Merkezi'ndeki Otomatik Av panelinden yönetilir.
        </p>
      </div>
    </div>

    <div class="izgara i3">
      <div class="levha tiklanir rv" data-modal
           data-baslik="1. Kilidi aç"
           data-üst='<span class="rozet">Geçme sayısı</span>'
           data-govde="<p>Bir keşfi belirli sayıda tamamladığında o keşfin otomatik av kilidi açılır. Panelde her keşfin yanında geçme sayın ve kilit eşiği yazar.</p><p>Kilit her zorluk kademesi için ayrıdir: Kolay'i açtığın, Zor'u açtığın anlamına gelmez.</p>">
        <div class="flama altin">01</div><h3>Kilidi aç</h3>
        <p>Aynı keşfi yeterince geç. Panelde ilerlemen 0/10 şeklinde görünür.</p>
        <span class="rozet" style="margin-top:14px">Detay için tıkla</span>
      </div>

      <div class="levha tiklanir v rv" data-modal
           data-baslik="2. Kayıt eli al"
           data-üst='<span class="rozet mavi">TEK BAŞINA girilmeli</span>'
           data-govde="<p>Otomatik av, senin o keşfi ne kadar sürede bitirdiğini baz alır. Bunu öğrenmesi için bir kez ölçüm yapması gerekir.</p><p>Panelde <b>Kayıt Eli Al</b> dedikten sonra o keşfe <b>tek başına</b> girip bitirmelisin. Yanında başka oyuncu varsa süre kaydedilmez, cunku güçlu bir arkadasla girip haksız kısa süre kaydetmek mümkün olurdu.</p><p>Ölçülen süre yukarı yuvarlanır: 12 saniye ise 15 saniye olarak kaydedilir. Kayıt elin bozulmaz, tek başına tekrar deneyebilirsin.</p>">
        <div class="flama altin">02</div><h3>Kayıt eli al</h3>
        <p>Keşfe <b>tek başına</b> girip bitir. Süren ölçülür ve yukarı yuvarlanarak kaydedilir.</p>
        <span class="rozet mavi" style="margin-top:14px">Detay için tıkla</span>
      </div>

      <div class="levha tiklanir g rv" data-modal
           data-baslik="3. Ava bırak ve topla"
           data-üst='<span class="rozet">Enerji peşin düşer</span>'
           data-govde="<p>Kaç el bırakacağını seçersin. Enerji <b>pesin</b> düşülür; böylece sen çevrimdışıyken enerjin başka yerde harcanamaz. Durdurursan başlamamış ellerin enerjisi iade edilir.</p><p>Her el, o keşfin kart açma sayısı kadar ödül çekilişi yapar. Yani ödül doğrudan o keşfin kendi drop tablosundan gelir.</p><p>Dönünce <b>Ödülleri Al</b> dersin. Çantan doluysa sığmayan eşyalar postaya gönderilir, hiçbir ödül kaybolmaz.</p>">
        <div class="flama altin">03</div><h3>Ava bırak</h3>
        <p>El sayısını seç, enerji peşin düşülsün. Dönünce <b>Ödülleri Al</b> ile topla.</p>
        <span class="rozet" style="margin-top:14px">Detay için tıkla</span>
      </div>
    </div>
  </div>
</section>

<!-- ================= GELISIM ================= -->
<section class="bolum" id="gelisim">
  <div class="wrap">
    <div class="baslik">
      <div class="rv">
        <span class="tepe">İlerleme</span>
        <h2>Karakterini <span class="vurgu">nasıl güçlendirirsin</span></h2>
      </div>
    </div>
    <div class="izgara i3">
      <div class="levha rv"><h3>Güçlendirme</h3>
        <p style="margin-top:8px">Demirci Atölyesi'nde ekipmanını seviye seviye güçlendirirsin. Seviye arttıkça başarı şansı düşer, taş kullanmak şansı yükseltir.</p></div>
      <div class="levha rv"><h3>İnci ve efsun</h3>
        <p style="margin-top:8px">Ekipmana inci takarak ek nitelik verirsin. Efsun ise silahın temel gücünü artıran ayrı bir katmandır.</p></div>
      <div class="levha rv"><h3>Evcil hayvan</h3>
        <p style="margin-top:8px">Evcil hayvanlar savaşta yanında dövüşur ve pasif nitelik verir. Yumurtadan çıkarılır, beslenerek geliştirilir.</p></div>
      <div class="levha rv"><h3>Binek</h3>
        <p style="margin-top:8px">Binekler hem görünüm hem nitelik sağlar. Binek incisiyle ek güç kazanırlar.</p></div>
      <div class="levha rv"><h3>Birlik</h3>
        <p style="margin-top:8px">Birliğe katılarak birlik görevleri, birlik savaşi ve ortak ödüllerden pay alırsın.</p></div>
      <div class="levha rv"><h3>VIP</h3>
        <p style="margin-top:8px">VIP; günlük ödül, ek tezgah hakkı ve bazı sistemlerde ayrıcalik sağlar.</p></div>
    </div>
  </div>
</section>

<?php include('parts/alt.php'); ?>
