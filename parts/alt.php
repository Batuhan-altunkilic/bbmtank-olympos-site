<?php
/* BBMTANK OLYMPOS — ORTAK ALT KABUK v2 (24.08.2026)
   Footer SOLA hizali. Modal kabugu ve Zeus balonu burada. */
?>
<footer class="oly-alt">
  <div class="wrap">
    <div class="sutunlar">
      <div>
        <a class="oly-marka" href="Anasayfa.php" style="margin-bottom:14px">
          <img src="assets/olympos/logo-sm.png" alt="BBMTank Olympos" width="52" height="52"
               loading="lazy" decoding="async"><b>BBMTANK</b>
        </a>
        <p style="color:var(--metin-sonuk);font-size:14.5px;line-height:1.75;max-width:44ch;margin:0">
          Olympos'un tepesinde sıra tabanlı topçu savaşları. Açıyı ayarla, gücü tuttur,
          rüzgârı hesapla. Kurulum yok, tarayıcıdan gir ve savaş.
        </p>
      </div>
      <div>
        <h4>Oyun</h4>
        <a href="index.php">Hemen Oyna</a>
        <a href="rehber.php">Oyun Rehberi</a>
        <a href="kesifler.php">Keşifler</a>
        <a href="siralama.php">Sıralama</a>
      </div>
      <div>
        <h4>Rehber</h4>
        <a href="rehber.php#savas">Savaş Mekanikleri</a>
        <a href="rehber.php#pazar">Pazar Alanı</a>
        <a href="rehber.php#otoav">Otomatik Av</a>
        <a href="rehber.php#gelisim">Karakter Gelişimi</a>
      </div>
      <div>
        <h4>Destek</h4>
        <a href="destek.php">Sıkça Sorulanlar</a>
        <a href="destek.php#flash">Flash Kurulumu</a>
        <a href="https://discord.gg/xbVEbfhVbw" target="_blank" rel="noopener">Discord Sunucusu</a>
        <a href="register.php">Ücretsiz Kayıt</a>
      </div>
    </div>
    <div class="dip">
      <span>&copy; <?php echo date('Y'); ?> BBMTank Olympos. Tüm hakları saklıdır.</span>
      <span>Sunucu saati: <?php echo date('d.m.Y H:i'); ?></span>
    </div>
  </div>
</footer>

<div class="oly-modal" id="olyModal" role="dialog" aria-modal="true">
  <div class="perde"></div>
  <div class="tablet">
    <button class="kapat" type="button" aria-label="Kapat">&times;</button>
    <div class="ust"></div>
    <h3></h3>
    <div class="govde"></div>
  </div>
</div>

<div class="zeus-tip" title="Yeni tavsiye için tıkla">
  <b>ZEUS'UN TAVSİYESİ</b>
  <span></span>
</div>

<script charset="utf-8" src="assets/bbm.js?v=20260824h"></script>
<script charset="utf-8" src="assets/eglence.js?v=20260824h"></script>
<?php if (!empty($sayfaScript)) { ?>
<script charset="utf-8" src="assets/<?php echo htmlspecialchars($sayfaScript); ?>?v=20260824h"></script>
<?php } ?>
</body>
</html>
