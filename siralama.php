<?php
/* =====================================================================
   BBMTANK OLYMPOS — SIRALAMA (24.08.2026)
   Canli DB sorgusu. Uydurma veri YOK; tablo bossa "veri yok" der.
   Sekmeler: seviye / savas gucu / onur / birlik
   ===================================================================== */
ini_set('default_charset', 'UTF-8');
include('global.php');

$t = isset($_GET['t']) ? strtolower($_GET['t']) : 'seviye';
$gecerli = array('seviye', 'guc', 'onur', 'birlik');
if (!in_array($t, $gecerli)) $t = 'seviye';

$satir = array();
$hata  = '';
co();

function _liste($sql) {
    $out = array();
    $rs = q($sql);
    if ($rs) while ($x = qa($rs)) $out[] = $x;
    return $out;
}

if ($t === 'seviye') {
    $baslikTablo = array('Savaşçı', 'Seviye', 'Tecrübe', 'Galibiyet');
    $satir = _liste("SELECT TOP 50 NickName, Grade, GP, Win, Total, ConsortiaID
                     FROM {$dbtank}.dbo.Sys_Users_Detail
                     WHERE IsExist = 1 AND UserName NOT LIKE '336bot%' AND UserName NOT LIKE 'botchar%' ORDER BY Grade DESC, GP DESC");
} elseif ($t === 'guc') {
    $baslikTablo = array('Savaşçı', 'Savaş Gücü', 'Seviye', 'Galibiyet');
    $satir = _liste("SELECT TOP 50 NickName, FightPower, Grade, Win, Total, ConsortiaID
                     FROM {$dbtank}.dbo.Sys_Users_Detail
                     WHERE IsExist = 1 AND UserName NOT LIKE '336bot%' AND UserName NOT LIKE 'botchar%' ORDER BY FightPower DESC, Grade DESC");
} elseif ($t === 'onur') {
    $baslikTablo = array('Savaşçı', 'Onur', 'Seviye', 'Galibiyet');
    $satir = _liste("SELECT TOP 50 NickName, Offer, Grade, Win, Total, ConsortiaID
                     FROM {$dbtank}.dbo.Sys_Users_Detail
                     WHERE IsExist = 1 AND UserName NOT LIKE '336bot%' AND UserName NOT LIKE 'botchar%' ORDER BY Offer DESC, Grade DESC");
} else {
    $baslikTablo = array('Birlik', 'Seviye', 'Üye', 'Hazine');
    $satir = _liste("SELECT TOP 50 ConsortiaName, Level, [Count], Riches, ChairmanName
                     FROM {$dbtank}.dbo.Consortia
                     ORDER BY Level DESC, Riches DESC, Count DESC");
}

/* birlik adlarini tek sorguda cek (oyuncu sekmelerinde gostermek icin) */
$birlikAd = array();
if ($t !== 'birlik') {
    $rs = q("SELECT ConsortiaID, ConsortiaName FROM {$dbtank}.dbo.Consortia");
    if ($rs) while ($x = qa($rs)) $birlikAd[(int)$x['ConsortiaID']] = $x['ConsortiaName'];
}

function _sayi($n) { return number_format((int)$n, 0, ',', '.'); }

$sayfaBaslik   = 'Sıralama';
$sayfaAciklama = 'BBMTank Olympos canlı sıralaması: seviye, savaş gücü, onur ve birlik listeleri. Zirvede kim var, anlık olarak gör.';
$sayfaAnahtar  = 'bbmtank sıralama, olympos lider tablosu, en güçlü oyuncu, birlik sıralaması';
include('parts/ust.php');
?>

<section class="bolum" style="padding-top:56px">
  <div class="wrap">

    <div class="baslik rv">
      <div class="tepe">Olympos zirvesi</div>
      <h1>Kim gerçekten <span class="vurgu">tepede?</span></h1>
      <p>
        Aşağıdaki liste doğrudan sunucu veritabanından geliyor; elle yazılmış tek satır yok.
        Sıralamada adını görmek istiyorsan tek yol var: oynamak.
      </p>
    </div>

    <div class="sekmeler rv">
      <a href="siralama.php?t=seviye"<?php echo $t==='seviye'?' class="on"':''; ?>>Seviye</a>
      <a href="siralama.php?t=guc"<?php echo $t==='guc'?' class="on"':''; ?>>Savaş Gücü</a>
      <a href="siralama.php?t=onur"<?php echo $t==='onur'?' class="on"':''; ?>>Onur</a>
      <a href="siralama.php?t=birlik"<?php echo $t==='birlik'?' class="on"':''; ?>>Birlikler</a>
    </div>

    <div class="sarma rv">
      <table class="tablo">
        <thead>
          <tr>
            <th>#</th>
            <?php foreach ($baslikTablo as $b) echo '<th>' . $b . '</th>'; ?>
            <?php if ($t !== 'birlik') echo '<th>Birlik</th>'; else echo '<th>Başkan</th>'; ?>
          </tr>
        </thead>
        <tbody>
        <?php if ($satir): $i = 0; foreach ($satir as $o): $i++; ?>
          <tr>
            <td class="sira"><?php echo $i; ?></td>
            <?php if ($t === 'birlik'): ?>
              <td class="isim"><?php echo htmlspecialchars($o['ConsortiaName']); ?></td>
              <td><?php echo (int)$o['Level']; ?></td>
              <td><?php echo (int)$o['Count']; ?></td>
              <td><?php echo _sayi($o['Riches']); ?></td>
              <td><?php echo htmlspecialchars($o['ChairmanName']); ?></td>
            <?php else: ?>
              <td class="isim"><?php echo htmlspecialchars($o['NickName']); ?></td>
              <?php if ($t === 'seviye'): ?>
                <td><?php echo (int)$o['Grade']; ?></td>
                <td><?php echo _sayi($o['GP']); ?></td>
              <?php elseif ($t === 'guc'): ?>
                <td><?php echo _sayi($o['FightPower']); ?></td>
                <td><?php echo (int)$o['Grade']; ?></td>
              <?php else: ?>
                <td><?php echo _sayi($o['Offer']); ?></td>
                <td><?php echo (int)$o['Grade']; ?></td>
              <?php endif; ?>
              <td>
                <?php
                  $w = (int)$o['Win']; $tp = (int)$o['Total'];
                  echo _sayi($w);
                  if ($tp > 0) echo ' <span style="color:var(--altin);font-size:12.5px">%' . round($w * 100 / $tp) . '</span>';
                ?>
              </td>
              <td style="color:var(--metin-sonuk)">
                <?php
                  $cid = (int)$o['ConsortiaID'];
                  echo ($cid && isset($birlikAd[$cid])) ? htmlspecialchars($birlikAd[$cid]) : '—';
                ?>
              </td>
            <?php endif; ?>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="6" style="text-align:center;padding:30px">
            Bu listede henüz veri yok. Sunucu yeni açıldıysa ilk sırayı sen kapabilirsin.
          </td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <p class="aciklama rv" style="margin:22px auto 0;text-align:center">
      Liste ilk 50 kaydı gösterir ve sayfa her açıldığında yeniden hesaplanır.
      Savaş gücü ekipman, güçlendirme, inci ve pet katkılarının toplamıdır.
    </p>

    <div style="text-align:center;margin-top:30px" class="rv">
      <a class="btn" href="index.php">Sıralamaya Gir</a>
    </div>

  </div>
</section>

<?php include('parts/alt.php'); ?>
