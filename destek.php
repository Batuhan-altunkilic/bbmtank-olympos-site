<?php
/* =====================================================================
   BBMTANK OLYMPOS — DESTEK / SSS (24.08.2026)
   ===================================================================== */
ini_set('default_charset', 'UTF-8');
include('global.php');

$sayfaBaslik   = 'Destek';
$sayfaAciklama = 'BBMTank Olympos destek merkezi: oyun açılmıyorsa, ekran beyaz kalıyorsa, şifreni unuttuysan ne yapman gerektiği adım adım burada.';
$sayfaAnahtar  = 'bbmtank destek, oyun açılmıyor, beyaz ekran, flash kurulumu, şifremi unuttum';
include('parts/ust.php');
?>

<section class="bolum" style="padding-top:56px">
  <div class="wrap">

    <div class="baslik rv">
      <div class="tepe">Yardım masası</div>
      <h1>Bir sorun mu var? <span class="vurgu">Çözelim.</span></h1>
      <p>
        Aşağıdakiler en sık gelen sorular. Cevabını burada bulamazsan Discord sunucumuza uğra,
        orada canlı yanıt alırsın.
      </p>
    </div>

    <div class="izgara i3 rv" style="margin-bottom:52px">
      <div class="levha" style="text-align:center;display:flex;flex-direction:column">
        <div class="ikon" style="margin:0 auto 12px">💬</div>
        <h3>Discord</h3>
        <p>En hızlı yol. Soru sor, hata bildir, oyuncularla tanış.</p>
        <div style="margin-top:auto;padding-top:18px">
          <a class="btn mavi" href="https://discord.gg/xbVEbfhVbw" target="_blank" rel="noopener">Sunucuya Katıl</a>
        </div>
      </div>
      <div class="levha" style="text-align:center;display:flex;flex-direction:column">
        <div class="ikon" style="margin:0 auto 12px">🔑</div>
        <h3>Şifre Kurtarma</h3>
        <p>Hesabına giremiyorsan şifreni buradan sıfırlayabilirsin.</p>
        <div style="margin-top:auto;padding-top:18px">
          <a class="btn" href="getnewpass.php">Şifremi Sıfırla</a>
        </div>
      </div>
      <div class="levha" style="text-align:center;display:flex;flex-direction:column">
        <div class="ikon" style="margin:0 auto 12px">📖</div>
        <h3>Oyun Rehberi</h3>
        <p>Oynanış, savaş mekanikleri ve sistemler tek tek anlatıldı.</p>
        <div style="margin-top:auto;padding-top:18px">
          <a class="btn mavi" href="rehber.php">Rehberi Aç</a>
        </div>
      </div>
    </div>

    <!-- ============ SSS ============ -->
    <div class="baslik rv" style="margin-bottom:26px">
      <div class="tepe">Sıkça sorulanlar</div>
      <h2>Merak edilenler</h2>
    </div>

    <div class="sss rv" id="flash">
      <details open>
        <summary>Oyun ekranı beyaz kalıyor / açılmıyor, ne yapmalıyım?</summary>
        <div class="cevap">
          Oyun Flash içerik çalıştırır. Ekran beyaz kalıyorsa sırayla şunları dene:
          <ol>
            <li><b>Sayfayı sert yenile:</b> <kbd>Ctrl</kbd> + <kbd>F5</kbd>. Eski önbellek en sık sebeptir.</li>
            <li><b>Tarayıcı eklentilerini kapat.</b> Reklam engelleyiciler oyun dosyalarını bloklayabiliyor.</li>
            <li><b>Gizli sekmede dene.</b> Orada açılıyorsa sorun eklenti ya da önbellektedir.</li>
            <li><b>Başka tarayıcı dene.</b> Flash desteği olan bir tarayıcı gerekir.</li>
          </ol>
          Hâlâ açılmıyorsa Discord'a ekran görüntüsüyle yaz; hangi tarayıcı ve sürüm olduğunu belirt.
        </div>
      </details>

      <details>
        <summary>Oyun yavaş açılıyor, takılıyor mu?</summary>
        <div class="cevap">
          İlk giriş her zaman en uzunudur; oyun dosyaları o sırada indirilir ve tarayıcıya
          önbelleklenir. İkinci girişten sonra açılış belirgin şekilde hızlanır.
          Yükleme ekranındaki çubuk dolarken sayfayı kapatma; yarıda kesilen indirme
          bir sonraki girişte baştan başlar.
        </div>
      </details>

      <details>
        <summary>Hesap açmak ücretli mi? İçeride para harcamam gerekiyor mu?</summary>
        <div class="cevap">
          Hayır. Kayıt ücretsiz, oynamak ücretsiz. Sunucu bir topluluk sunucusudur;
          seviye atlamak, keşif geçmek, ekipman güçlendirmek için para harcaman gerekmez.
        </div>
      </details>

      <details>
        <summary>Şifremi unuttum, hesabımı nasıl kurtarırım?</summary>
        <div class="cevap">
          <a href="getnewpass.php" style="color:var(--altin-isik);text-decoration:underline">Şifre sıfırlama sayfasından</a>
          kayıt olurken verdiğin e-posta adresiyle yeni şifre alabilirsin.
          E-posta adresini de hatırlamıyorsan Discord üzerinden karakter adınla birlikte yaz.
        </div>
      </details>

      <details>
        <summary>Karakter adımı ya da kullanıcı adımı değiştirebilir miyim?</summary>
        <div class="cevap">
          Karakter adı oyun içinden <b>isim değiştirme kartı</b> ile değiştirilir.
          Kullanıcı adı ise hesabın kimliğidir ve değiştirilemez; yeni bir kullanıcı adı
          istiyorsan yeni hesap açman gerekir.
        </div>
      </details>

      <details>
        <summary>Güç barını bir türlü aynı yerde durduramıyorum.</summary>
        <div class="cevap">
          Durdurman gerekmiyor. <b>Güç barının üstünde istediğin noktaya tıkla</b>, oraya bir
          işaret konur. Sonra <kbd>M</kbd> tuşuna bas; güç o işarete kadar kendi kendine dolup
          durur. Aynı mesafeye üst üste atarken elle tutturmaya çalışmaktan çok daha isabetli.
        </div>
      </details>

      <details>
        <summary>Otomatik Av'da neden ilerleme olmuyor?</summary>
        <div class="cevap">
          Üç sebepten biridir:
          <ul>
            <li>O keşfin <b>kilidi henüz açılmamıştır</b>. Kilit, aynı keşfi yeterince geçtiğinde açılır.</li>
            <li>O keşif için <b>kayıt eli alınmamıştır</b>. Kayıt eli, keşfe <b>tek başına</b> girip
                bitirdiğinde alınır; yanında biri varsa süre kaydedilmez.</li>
            <li><b>Enerjin bitmiştir.</b> Otomatik av her el için enerji harcar.</li>
          </ul>
        </div>
      </details>

      <details>
        <summary>Tezgâh kurmak için VIP olmam gerekiyor mu?</summary>
        <div class="cevap">
          Hayır. Herkesin <b>bir</b> tezgâh hakkı vardır. VIP seviyesi yükseldikçe aynı anda
          kurabileceğin tezgâh sayısı artar, ama tezgâh açmanın kendisi için VIP şartı yoktur.
        </div>
      </details>

      <details>
        <summary>Çantam doldu, ödüllerim kayboluyor mu?</summary>
        <div class="cevap">
          Kaybolmaz. Çanta doluyken düşen eşyalar <b>posta kutuna</b> gönderilir.
          Çantanda yer açtıktan sonra postadan teslim alabilirsin.
        </div>
      </details>

      <details>
        <summary>Hile kullananları nasıl bildiririm?</summary>
        <div class="cevap">
          Discord'daki bildirim kanalına <b>karakter adı</b>, <b>tarih/saat</b> ve varsa
          <b>ekran görüntüsü veya video</b> ile yaz. İsimsiz ihbarlar işleme alınmaz;
          kanıtlı bildirimler incelenip yaptırım uygulanır.
        </div>
      </details>

      <details>
        <summary>Sunucu neden bazen kapanıyor?</summary>
        <div class="cevap">
          Güncelleme ve bakım sırasında kısa süreli kapanır. Planlı bakımlar Discord'da
          önceden duyurulur. Bakım sırasındaki kesintide karakterin ve eşyaların kaybolmaz.
        </div>
      </details>
    </div>

    <div class="cagri rv" style="margin-top:52px">
      <h2>Cevabını bulamadın mı?</h2>
      <p>
        Discord'a gel, sorunu yaz. Aynı sorunu daha önce yaşamış biri mutlaka vardır;
        yoksa da biz bakarız.
      </p>
      <div class="dugmeler">
        <a class="btn" href="https://discord.gg/xbVEbfhVbw" target="_blank" rel="noopener">Discord'a Katıl</a>
        <a class="btn mavi" href="rehber.php">Rehbere Göz At</a>
      </div>
    </div>

  </div>
</section>

<?php include('parts/alt.php'); ?>
