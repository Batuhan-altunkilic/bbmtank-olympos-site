<?php
/* =====================================================================
   BBMTANK OLYMPOS — OYUNCU PANELI (24.08.2026)
   ---------------------------------------------------------------------
   Eski index.php yabanci bir siteden devralinmisti: baska bir hesaba ait
   Facebook Pixel (295621851491418), Google Analytics (G-1EHFCWTEMR) ve
   googletagservices reklam slotlari ziyaretci verisini uclu tarafa
   gonderiyordu; ayrica rakip sunucunun Facebook sayfasi gomuluydu.
   Hepsi kaldirildi. Bu sayfa artik sadece kendi verimizi gosterir.
   ===================================================================== */
ini_set('default_charset', 'UTF-8');
include('global.php');

if (!isset($_SESSION['UserId']) || isset($_GET['logout'])) {
    session_destroy();
    die('<script>window.location="Anasayfa.php";</script>');
}

$kullanici = $_SESSION['UserName'];
$takma     = isset($_SESSION['NickName']) ? $_SESSION['NickName'] : $kullanici;
$vip       = isset($_SESSION['IsVip']) ? (int)$_SESSION['IsVip'] : 0;
$kupon     = isset($_SESSION['Coin']) ? (int)$_SESSION['Coin'] : 0;

/* --- karakter verisi (canli) --- */
$ben = null; $birlik = ''; $sira = 0;
co();
$ben = qa(q("SELECT TOP 1 NickName, Grade, GP, Gold, Money, Offer, Win, Total, [Escape],
                    FightPower, ConsortiaID, LoginCount, Date
             FROM {$dbtank}.dbo.Sys_Users_Detail
             WHERE UserName = '" . addslashes($kullanici) . "'"));
if ($ben) {
    $cid = (int)$ben['ConsortiaID'];
    if ($cid > 0) {
        $c = qa(q("SELECT TOP 1 ConsortiaName FROM {$dbtank}.dbo.Consortia WHERE ConsortiaID = {$cid}"));
        if ($c) $birlik = $c['ConsortiaName'];
    }
    $r = qa(q("SELECT COUNT(*) AS a FROM {$dbtank}.dbo.Sys_Users_Detail
               WHERE Grade > " . (int)$ben['Grade'] . "
                  OR (Grade = " . (int)$ben['Grade'] . " AND GP > " . (int)$ben['GP'] . ")"));
    if ($r) $sira = (int)$r['a'] + 1;
}

function _s($n) { return number_format((int)$n, 0, ',', '.'); }

$galip = $ben ? (int)$ben['Win'] : 0;
$mac   = $ben ? (int)$ben['Total'] : 0;
$oran  = $mac > 0 ? round($galip * 100 / $mac) : 0;

$sayfaBaslik   = $takma;
$sayfaAciklama = 'BBMTank Olympos oyuncu paneli.';
include('parts/ust.php');
?>

<section class="bolum" style="padding:44px 0 24px">
  <div class="wrap">

    <!-- ======== KARŞILAMA + OYNA ======== -->
    <div class="cagri rv" style="text-align:left;padding:38px 34px">
      <div style="position:relative;display:flex;gap:26px;align-items:center;flex-wrap:wrap">
        <div style="flex:1 1 320px;min-width:280px">
          <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px">
            <span class="rozet">Seviye <?php echo $ben ? (int)$ben['Grade'] : 0; ?></span>
            <?php if ($birlik): ?><span class="rozet mavi"><?php echo htmlspecialchars($birlik); ?></span><?php endif; ?>
            <?php if ($vip): ?><span class="rozet">VIP</span><?php endif; ?>
            <?php if ($sira): ?><span class="rozet mavi">Sıralama #<?php echo $sira; ?></span><?php endif; ?>
          </div>
          <h2 style="margin:0 0 8px;text-align:left">Hoş geldin, <?php echo htmlspecialchars($takma); ?></h2>
          <p style="margin:0;text-align:left;max-width:52ch">
            Meydan hazır, rüzgâr senden yana. Aşağıdaki düğmeye bas, oyun doğrudan tarayıcıda açılsın.
          </p>
        </div>
        <div style="flex:0 0 auto">
          <a class="btn" href="play.php" style="font-size:19px;padding:20px 46px">⚔️ OYNA</a>
          <div style="margin-top:12px;text-align:center">
            <a href="index.php?logout=true" style="color:var(--metin-sonuk);font-size:13.5px;text-decoration:underline">Çıkış yap</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ======== KARAKTER SAYAÇLARI ======== -->
<div class="serit" style="margin-top:0">
  <div class="wrap">
    <div class="ic">
      <div class="kutu"><b data-say="<?php echo $ben ? (int)$ben['FightPower'] : 0; ?>">0</b><span>Savaş Gücü</span></div>
      <div class="kutu"><b data-say="<?php echo $galip; ?>">0</b><span>Galibiyet</span></div>
      <div class="kutu"><b data-say="<?php echo $oran; ?>" data-son="%">0</b><span>Kazanma Oranı</span></div>
      <div class="kutu"><b data-say="<?php echo $ben ? (int)$ben['Offer'] : 0; ?>">0</b><span>Onur</span></div>
    </div>
  </div>
</div>

<section class="bolum" style="padding-top:64px">
  <div class="wrap">
    <div class="izgara i3">

      <!-- ---- cüzdan ---- -->
      <div class="levha parsomen rv">
        <span class="flama altin">Cüzdan</span>
        <h3>Kasan ne durumda?</h3>
        <table style="width:100%;border-collapse:collapse;margin-top:8px">
          <tr><td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35)">Altın</td>
              <td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35);text-align:right;font-weight:900"><?php echo _s($ben ? $ben['Gold'] : 0); ?></td></tr>
          <tr><td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35)">Para</td>
              <td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35);text-align:right;font-weight:900"><?php echo _s($ben ? $ben['Money'] : 0); ?></td></tr>
          <tr><td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35)">Kupon</td>
              <td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35);text-align:right;font-weight:900"><?php echo _s($kupon); ?></td></tr>
          <tr><td style="padding:9px 0">Tecrübe</td>
              <td style="padding:9px 0;text-align:right;font-weight:900"><?php echo _s($ben ? $ben['GP'] : 0); ?></td></tr>
        </table>
      </div>

      <!-- ---- karne ---- -->
      <div class="levha parsomen rv">
        <span class="flama mavi">Karne</span>
        <h3>Savaş geçmişin</h3>
        <table style="width:100%;border-collapse:collapse;margin-top:8px">
          <tr><td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35)">Toplam maç</td>
              <td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35);text-align:right;font-weight:900"><?php echo _s($mac); ?></td></tr>
          <tr><td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35)">Galibiyet</td>
              <td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35);text-align:right;font-weight:900"><?php echo _s($galip); ?></td></tr>
          <tr><td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35)">Kaçış</td>
              <td style="padding:9px 0;border-bottom:1px dashed rgba(138,90,30,.35);text-align:right;font-weight:900"><?php echo _s($ben ? $ben['Escape'] : 0); ?></td></tr>
          <tr><td style="padding:9px 0">Giriş sayısı</td>
              <td style="padding:9px 0;text-align:right;font-weight:900"><?php echo _s($ben ? $ben['LoginCount'] : 0); ?></td></tr>
        </table>
      </div>

      <!-- ---- kısayollar ---- -->
      <div class="levha rv">
        <span class="flama">Kısayol</span>
        <h3>Nereye gitsen?</h3>
        <a class="haber" href="rehber.php#otoav"><span class="tar">Oto Av</span> Keşiflerini gece boyunca çalıştır</a>
        <a class="haber" href="kesifler.php"><span class="tar">Keşif</span> Hangi bölge hangi seviyeye uygun?</a>
        <a class="haber" href="siralama.php"><span class="tar">Sıralama</span> Zirvede kim var, kaçıncısın?</a>
        <a class="haber" href="rehber.php#pazar"><span class="tar">Pazar</span> Tezgâh kurup eşya sat</a>
        <a class="haber" href="destek.php"><span class="tar">Destek</span> Bir sorun mu çıktı?</a>
      </div>

    </div>
  </div>
</section>

<!-- ======== DUYURU ======== -->
<section class="bolum" style="padding-top:0">
  <div class="wrap">
    <div class="izgara i2">
      <div class="levha parsomen rv">
        <span class="flama">Duyuru</span>
        <h3>Sunucudan son haberler</h3>
        <a class="haber" href="rehber.php#otoav"><span class="tar">24.08</span> Otomatik Av açıldı. Kilidini açtığın keşifleri artık uyurken çalıştırabilirsin.</a>
        <a class="haber" href="rehber.php#pazar"><span class="tar">23.08</span> Tezgâh kurmak için VIP şartı kaldırıldı; herkesin bir tezgâh hakkı var.</a>
        <a class="haber" href="kesifler.php"><span class="tar">22.08</span> On altı keşif on altı ayrı haritaya taşındı, çakışma sorunu bitti.</a>
        <a class="haber" href="rehber.php#savas"><span class="tar">21.08</span> Güç barına işaret koyup <b>M</b> tuşuyla otomatik doldurma eklendi.</a>
      </div>
      <div class="levha rv" style="display:flex;flex-direction:column;justify-content:center;text-align:center">
        <div class="ikon" style="margin:0 auto 14px">💬</div>
        <h3>Topluluğa katıl</h3>
        <p style="margin-bottom:18px">
          Sorunu sor, birlik kur, etkinlikleri kaçırma. Sunucunun asıl sohbeti Discord'da dönüyor.
        </p>
        <div>
          <a class="btn mavi" href="https://discord.gg/xbVEbfhVbw" target="_blank" rel="noopener">Discord Sunucusu</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include('parts/alt.php'); ?>
