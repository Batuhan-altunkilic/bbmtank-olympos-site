<?php
/* =====================================================================
   BBMTank — KESIFLER  (24.08.2026)
   Veri UYDURULMADI: keşifler ve hikayeleri doğrudan Pve_Info tablosundan
   okunur (Type 4 = keşif, Type 26 = kabus keşfi).
   ===================================================================== */
ini_set('default_charset', 'UTF-8');
include('global.php');
co();   // q() 'global $conn' kullaniyor; baglantiyi co() aciyor

$sayfaAd       = 'keşifler';
$sayfaBaslik   = 'Keşifler';
$sayfaAciklama = 'BBMTank keşif odaları: 16 bossun her birinin kendi haritası, hikâyesi ve drop tablosu var. Seviye şartlarını ve zorluk kademelerini incele.';
$sayfaAnahtar  = 'bbmtank keşif, boss, zindan, bati kralicesi, kara buyucu, kabus keşfi';

/* --- keşifleri cek --- */
$keşifler = array('normal' => array(), 'kabus' => array());
$sorgu = q("SELECT ID, Name, LevelLimits, Description, Type, ISNULL(Ordering,0) AS Sira
            FROM {$dbtank}.dbo.Pve_Info WHERE Type IN (4,26) ORDER BY Type, ISNULL(Ordering,0)");
if ($sorgu) {
  while ($s = qa($sorgu)) {
    $grup = ((int)$s['Type'] === 4) ? 'normal' : 'kabus';
    $keşifler[$grup][] = $s;
  }
}
$toplam = count($keşifler['normal']) + count($keşifler['kabus']);
include('parts/ust.php');

function keşifKart($k, $tur) {
  $ad   = trim($k['Name']);
  $sev  = (int)$k['LevelLimits'];
  $anlt = trim(preg_replace('/\s+/u', ' ', (string)$k['Description']));
  if ($anlt === '') $anlt = 'Bu keşfin hikâyesi yakında eklenecek.';
  $kısa = mb_substr($anlt, 0, 118, 'UTF-8');
  if (mb_strlen($anlt, 'UTF-8') > 118) $kısa .= '...';

  $rozet = ($tur === 'kabus')
    ? '<span class="rozet mavi">Kabus Keşfi</span>'
    : '<span class="rozet">Keşif</span>';
  $şerit = ($tur === 'kabus') ? 'stripe v' : 'stripe';

  $govde = '<p>' . htmlspecialchars($anlt, ENT_QUOTES, 'UTF-8') . '</p>'
    . '<p style="margin-top:14px"><b>Seviye şartı:</b> ' . $sev . '</p>'
    . '<p><b>Zorluk kademeleri:</b> Kolay, Normal, Zor, Dehşet, Epik. '
    . 'Kademe yükseldikçe bossun canı ve ödülün değeri birlikte artar.</p>'
    . '<p><b>Yapi:</b> Tek boss. Ara kat yok; bossu indirdiğinde keşif biter '
    . 've kart açma hakkın kadar ödül çekilişi yapılır.</p>';
  $üst = $rozet . ' <span class="rozet mavi">Seviye ' . $sev . '</span>';

  echo '<article class="levha tiklanir ' . $şerit . ' rv" data-modal'
     . ' data-baslik="' . htmlspecialchars($ad, ENT_QUOTES, 'UTF-8') . '"'
     . ' data-üst="' . htmlspecialchars($üst, ENT_QUOTES, 'UTF-8') . '"'
     . ' data-govde="' . htmlspecialchars($govde, ENT_QUOTES, 'UTF-8') . '">'
     . '<div class="flama altin">SEVİYE ' . $sev . '</div>'
     . '<h3>' . htmlspecialchars($ad, ENT_QUOTES, 'UTF-8') . '</h3>'
     . '<p>' . htmlspecialchars($kısa, ENT_QUOTES, 'UTF-8') . '</p>'
     . '<span class="pill ' . ($tur === 'kabus' ? 'vi' : 'gold') . '" style="margin-top:14px">Hikâyeyi oku</span>'
     . '</article>';
}
?>

<section class="bolum" id="üst">
  <div class="wrap">
    <div class="baslik">
      <div class="rv">
        <span class="tepe">Keşif Rıhtımı</span>
        <h1 class="vurgu">16 boss,<br>16 ayrı dövüş</h1>
        <p class="aciklama">
          Her keşfin kendi bossu, kendi haritası ve kendi drop tablosu var.
          Ara kat yok: odaya girersin, bossu indirirsin, keşif biter.
          Seviyen yükseldikçe yeni odalar açılır.
        </p>
      </div>
      <div class="rv" style="display:flex;gap:44px;flex-wrap:wrap;justify-content:center;margin-top:28px">
        <div class="sayac"><b data-say="<?php echo $toplam; ?>">0</b><span>Keşif Odası</span></div>
        <div class="sayac"><b data-say="5">0</b><span>Zorluk</span></div>
        <div class="sayac"><b data-say="16">0</b><span>Seviye Alt Sınırı</span></div>
      </div>
    </div>
  </div>
</section>

<section class="bolum" id="normal">
  <div class="wrap">
    <div class="baslik">
      <div class="rv">
        <span class="tepe">Normal Uzak</span>
        <h2>Keşifler</h2>
        <p class="aciklama">Seviye 16'dan başlayarak açılan ana keşif odaları.</p>
      </div>
    </div>
    <div class="izgara i4">
      <?php foreach ($keşifler['normal'] as $k) keşifKart($k, 'normal'); ?>
      <?php if (!count($keşifler['normal'])) echo '<p class="aciklama">Keşif listesi şu an yüklenemedi.</p>'; ?>
    </div>
  </div>
</section>

<section class="bolum" id="kabus">
  <div class="wrap">
    <div class="baslik">
      <div class="rv">
        <span class="tepe">Kabus Sürümü</span>
        <h2 class="vurgu">Kabus Keşifleri</h2>
        <p class="aciklama">
          Seviye 50 ve sonrası için açılan üst kademe odalar. Bossları daha
          dayanıklı, ödülleri daha değerli.
        </p>
      </div>
    </div>
    <div class="izgara i4">
      <?php foreach ($keşifler['kabus'] as $k) keşifKart($k, 'kabus'); ?>
      <?php if (!count($keşifler['kabus'])) echo '<p class="aciklama">Kabus keşifleri şu an yüklenemedi.</p>'; ?>
    </div>
  </div>
</section>

<section class="bolum">
  <div class="wrap">
    <div class="izgara i2">
      <div class="levha rv">
        <h3>Keşifleri otomatiğe bağlamak</h3>
        <p style="margin-top:8px">
          Bir keşfi yeterince geçtiginde Otomatik Av kilidi açılır. Kayıt eli
          alıp süren ölçüldükten sonra o keşfi senin adına tekrar tekrar koşar.
        </p>
        <a class="btn mavi" style="margin-top:16px" href="rehber.php#otoav">Otomatik Av rehberi</a>
      </div>
      <div class="levha rv">
        <h3>Önce mekaniği öğren</h3>
        <p style="margin-top:8px">
          Açı, güç ve rüzgâr uçuşunu okumadan üst kademe bossları indirmek zor.
          Güç barına işaret koyup <kbd>M</kbd> ile ayni gücü tekrarlamayı öğren.
        </p>
        <a class="btn mavi" style="margin-top:16px" href="rehber.php#savas">Savaş mekanikleri</a>
      </div>
    </div>
  </div>
</section>

<?php include('parts/alt.php'); ?>
