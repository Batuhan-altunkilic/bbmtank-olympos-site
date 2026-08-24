<?php
#--- BBMTank ANA launcher: self-starting rich core (loading.swf YOK, DIRECT) ---
@ini_set('display_errors','0'); error_reporting(E_ERROR | E_PARSE);
session_start();
if(!isset($_SESSION['UserId'])) { echo '<script>window.location="index.php";</script>'; exit; }
$user = $_SESSION['UserName'];
$pass = isset($_SESSION['PassWord']) ? $_SESSION['PassWord'] : '';

$k = '';
$cj = tempnam(sys_get_temp_dir(), 'brc');
if(function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => 'http://bbmtank.com/checkuser.ashx?username='.urlencode($user).'&password='.urlencode($pass),
        CURLOPT_RETURNTRANSFER => 1, CURLOPT_COOKIEJAR => $cj, CURLOPT_COOKIEFILE => $cj, CURLOPT_TIMEOUT => 15
    ));
    $ok = trim(curl_exec($ch));
    if($ok === 'ok') {
        curl_setopt($ch, CURLOPT_URL, 'http://bbmtank.com/logingame.aspx');
        $keyurl = curl_exec($ch);
        if(preg_match('/key=([^&\s"\'<]+)/i', $keyurl, $m)) $k = $m[1];
    }
    curl_close($ch);
}
@unlink($cj);
if($k === '') { echo '<script>alert("Key uretilemedi. Tekrar gir.");window.location="index.php";</script>'; exit; }

# BBMTank FIX (2026-07-05): SWF URL'sinde cache-buster YOKtu -> tarayici eski bbmtank.swf'i
# cache'ten sunuyordu (pet penceresi fix'i gibi client guncellemeleri normal reload'da GELMIYORDU;
# kullanici elle cache temizlemek zorundaydi). Cozum: SWF dosyasinin mtime'ini ?v= olarak ekle ->
# her yeni build otomatik cache-bust olur, normal reload en guncel client'i ceker.
$swfpath = __DIR__.'/../Flash_rich/bbmtank.swf';
$swfver = @filemtime($swfpath); if(!$swfver) { $swfver = time(); }
# BBMTank FIX (2026-08-22): cache-buster SADECE ana SWF'in mtime'ini kullaniyordu.
# Dil paketi (language.png) veya bir UI modulu (ui/turkey/swf/*.swf) degisip ana SWF
# degismediginde surum ayni kaliyor -> tarayici ESKI dili/modulu sunuyordu; ekrandaki
# etiket bir turlu guncellenmiyordu. Artik surum = bu kaynaklarin EN YENI mtime'i.
$ekstra = array(
  __DIR__.'/../Flash_rich/ui/turkey/language.png',
  __DIR__.'/../Flash_rich/ui/turkey/xml.png',
);
foreach (glob(__DIR__.'/../Flash_rich/ui/turkey/swf/*.swf') as $m) { $ekstra[] = $m; }
foreach ($ekstra as $f) { $t = @filemtime($f); if ($t && $t > $swfver) { $swfver = $t; } }
$swf = 'http://bbmtank.com/flashrich/bbmtank.swf?v='.$swfver.'&user='.rawurlencode($user).'&key='.rawurlencode($k).'&config=http://bbmtank.com/config_rich.xml';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>BBMTank Olympos — Oyun</title>
<link rel="icon" type="image/png" href="assets/olympos/favicon.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;900&display=swap" rel="stylesheet">
<style>
/* =====================================================================
   OYUN ACILIS EKRANI — tasarim 8 birebir
   ---------------------------------------------------------------------
   Gorseldeki cubuk ve yazi yerleri piksel taramasiyla olculdu
   (_TASARIM/acilis_hazirla.py):  cubuk x 495-1421 / y 936-956 (1920x1080)
   Yuzdeye cevrildi: sol %25.781  genislik %48.281  ust %86.667  yuk %1.944
   Tuval 16:9 oraninda kilitlendigi icin bu yuzdeler her ekranda tutar.
   ===================================================================== */
:root{
  --altin:#d4a251; --altin-isik:#f4dca6; --altin-golge:#8a5a1e; --altin-derin:#503010;
  --mavi:#1a4d8c; --mavi-parlak:#2f6dbf; --simsek:#90ceee; --gece:#070c1a;
  --olcek:'Cinzel','Trajan Pro',Georgia,serif;
}
*{box-sizing:border-box}
html,body{margin:0;height:100%;overflow:hidden;background:var(--gece);
  font-family:'Segoe UI',Inter,system-ui,Arial,sans-serif}

.oyun-zemin{position:fixed;inset:0;z-index:0;
  background:
    radial-gradient(900px 520px at 50% -10%,rgba(244,220,166,.20) 0%,transparent 62%),
    radial-gradient(800px 620px at 12% 25%,rgba(26,77,140,.45) 0%,transparent 60%),
    linear-gradient(180deg,#0f1c3d 0%,#0b1226 55%,#070c1a 100%)}

.oyun-kap{position:relative;z-index:2;height:100vh;display:flex;align-items:center;justify-content:center}
.cerceve{position:relative;width:1000px;height:600px;border-radius:10px;overflow:hidden;
  border:2px solid var(--altin);background:#000;
  box-shadow:0 0 0 4px var(--altin-derin),0 40px 90px -30px #000,0 0 60px -14px rgba(212,162,81,.45)}
@media(max-width:1060px),(max-height:660px){.cerceve{transform:scale(.82);transform-origin:center}}

/* ---------- acilis katmani ---------- */
.acilis{position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;
  background:var(--gece);transition:opacity .65s ease,visibility .65s ease}
.acilis.bitti{opacity:0;visibility:hidden;pointer-events:none}

.acilis .tuval{--h:min(56.25vw,100vh);position:relative;height:var(--h);width:calc(var(--h) * 16 / 9);
  background:url('assets/olympos/acilis.jpg') center/100% 100% no-repeat}

/* ipucu: karakterlerin arasindaki bos mermer bant */
.acilis .ipucu{position:absolute;left:26%;right:26%;top:70.5%;text-align:center;
  font-size:calc(var(--h) * .0185);line-height:1.55;font-weight:600;color:#3a2405;
  text-shadow:0 1px 0 rgba(255,255,255,.95),0 0 14px rgba(255,255,255,.9),
              0 0 34px rgba(255,255,255,.75)}
.acilis .ipucu b{color:#7a4d12}
.acilis .ipucu i{display:block;font-family:var(--olcek);font-style:normal;
  font-size:calc(var(--h) * .0125);letter-spacing:.28em;text-transform:uppercase;
  color:#5a3308;opacity:1;margin-bottom:.5em;
  text-shadow:0 1px 0 rgba(255,255,255,.95),0 0 12px rgba(255,255,255,.85)}

/* durum yazisi: pisirilmis 'YUKLENIYOR...' ile ayni yer */
.acilis .durum{position:absolute;left:0;right:0;top:81.1%;text-align:center;
  font-family:var(--olcek);font-weight:700;font-size:calc(var(--h) * .036);letter-spacing:.10em;
  text-transform:uppercase;color:#42280a;
  text-shadow:0 1px 0 rgba(255,255,255,.95),0 0 16px rgba(255,255,255,.95),
              0 0 40px rgba(255,255,255,.8),0 3px 10px rgba(0,0,0,.2)}

/* cubuk: gorseldeki altin cercevenin TAM ICI */
.acilis .cubuk{position:absolute;left:25.781%;top:86.667%;width:48.281%;height:1.944%;
  overflow:hidden;border-radius:3px}
.acilis .cubuk i{position:relative;display:block;height:100%;width:0%;border-radius:2px;overflow:hidden;
  background:linear-gradient(90deg,#123a72 0%,var(--mavi-parlak) 42%,var(--simsek) 100%);
  box-shadow:0 0 16px rgba(144,206,238,.95),inset 0 1px 0 rgba(255,255,255,.45);
  transition:width .3s ease}
.acilis .cubuk i::after{content:"";position:absolute;inset:0;
  background:linear-gradient(90deg,transparent 60%,rgba(255,255,255,.35) 78%,transparent 92%);
  animation:parla 1.6s linear infinite}
@keyframes parla{0%{transform:translateX(-100%)}100%{transform:translateX(100%)}}

/* yuzde: pisirilmis '100%' ile ayni yer */
.acilis .yuzde{position:absolute;left:0;right:0;top:89.6%;text-align:center;
  font-family:var(--olcek);font-weight:900;font-size:calc(var(--h) * .034);letter-spacing:.04em;
  color:#42280a;text-shadow:0 1px 0 rgba(255,255,255,.95),0 0 16px rgba(255,255,255,.95),
              0 0 40px rgba(255,255,255,.8),0 3px 10px rgba(0,0,0,.2)}

.acilis .gec{position:absolute;right:2.2%;bottom:2.2%;font-size:calc(var(--h) * .0145);
  color:rgba(255,255,255,.55);letter-spacing:.06em}
</style>
</head>
<body>
<div class="oyun-zemin"></div>

<!-- ============ OLYMPOS AÇILIŞ EKRANI ============ -->
<div class="acilis" id="acilis">
  <div class="tuval">
    <div class="ipucu" id="ipucu"><i>Olympos'tan not</i><span></span></div>
    <div class="durum" id="durum">Olympos hazırlanıyor</div>
    <div class="cubuk"><i id="dolgu"></i></div>
    <div class="yuzde" id="yuzde">0%</div>
    <div class="gec">geçmek için tıkla</div>
  </div>
</div>

<div class="oyun-kap">
  <div class="cerceve">
    <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="1000" height="600" id="Main" name="Main">
      <param name="movie" value="<?php echo htmlspecialchars($swf); ?>" />
      <param name="allowScriptAccess" value="always" />
      <param name="allowNetworking" value="all" />
      <param name="quality" value="high" />
      <param name="menu" value="false" />
      <param name="bgcolor" value="#000000" />
      <param name="wmode" value="direct" />
      <embed src="<?php echo htmlspecialchars($swf); ?>" width="1000" height="600" align="middle"
        quality="high" name="Main" allowScriptAccess="always" allowNetworking="all" wmode="direct"
        type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" />
    </object>
  </div>
</div>

<script>
/* =====================================================================
   Batuhan: "oyun yuklenirken de yukleme ekrani gelmiyor".
   Gercek ilerleme Flash nesnesinin PercentLoaded() metodundan okunur.
   Metot yoksa yumusak bir rampa ile ilerler, %92'de bekler ve SWF
   hazir olunca kapanir. Her durumda 60 sn'lik guvenlik agi var.
   ===================================================================== */
(function () {
  "use strict";
  var NOTLAR = [
    "Güç barına tıklayıp <b>işaret koy</b>, sonra <b>M</b> tuşuna bas. Güç o işarete kadar kendi doluyor.",
    "Rüzgâr arkandan esiyorsa gücü bir tık <b>kıs</b>, karşıdan esiyorsa <b>aç</b>.",
    "Yüksek açı engel aşmak içindir. Düz atış hızlıdır ama duvarları sevmez.",
    "Keşif kaydı almak için odaya <b>tek başına</b> gir; yanında biri varsa süre sayılmaz.",
    "Çantan doluyken düşen ödül kaybolmaz, <b>postana</b> gider.",
    "Güçlendirmede yüksek seviyeye koruma eşyası olmadan zorlama; kırılma riski gerçek.",
    "Otomatik ava bırakmadan önce <b>enerjini</b> kontrol et. Her el enerji harcar.",
    "Tezgâh kurmak için VIP olman gerekmiyor; herkesin bir tezgâh hakkı var.",
    "İlk giriş her zaman en uzunudur. Dosyalar önbelleğe alınır, ikinci giriş çok daha hızlı."
  ];

  var acilis = document.getElementById("acilis"),
      dolgu  = document.getElementById("dolgu"),
      yuzde  = document.getElementById("yuzde"),
      durum  = document.getElementById("durum"),
      notEl  = document.getElementById("ipucu").querySelector("span");

  var o = 0, kapandi = false, bosTur = 0;

  function notYaz() { notEl.innerHTML = NOTLAR[Math.floor(Math.random() * NOTLAR.length)]; }
  notYaz();
  var notSayaci = setInterval(notYaz, 5400);

  function ciz(v) {
    o = Math.max(o, Math.min(100, v));
    dolgu.style.width = o + "%";
    yuzde.textContent = Math.round(o) + "%";
    if (o >= 99)      durum.textContent = "Meydana iniliyor";
    else if (o >= 70) durum.textContent = "Karakterin hazırlanıyor";
    else if (o >= 35) durum.textContent = "Savaş alanı yükleniyor";
    else              durum.textContent = "Olympos hazırlanıyor";
  }

  function flashYuzde() {
    var m = document.getElementById("Main");
    if (!m || typeof m.PercentLoaded !== "function") return -1;
    try { var p = m.PercentLoaded(); return (typeof p === "number" && p >= 0) ? p : -1; }
    catch (e) { return -1; }
  }

  function kapat() {
    if (kapandi) return;
    kapandi = true;
    clearInterval(notSayaci);
    ciz(100);
    setTimeout(function () {
      acilis.classList.add("bitti");
      setTimeout(function () { acilis.style.display = "none"; }, 750);
    }, 650);
  }

  var tik = setInterval(function () {
    var p = flashYuzde();
    if (p >= 0) {
      ciz(p);
      if (p >= 100) { clearInterval(tik); setTimeout(kapat, 2600); }
    } else {
      bosTur++;
      if (o < 92) ciz(o + (o < 55 ? 2.2 : 0.8));
      if (bosTur > 200) { clearInterval(tik); kapat(); }   /* ~40 sn */
    }
  }, 200);

  setTimeout(function () { clearInterval(tik); kapat(); }, 60000);
  acilis.addEventListener("click", function () { clearInterval(tik); kapat(); });
})();
</script>
</body>
</html>
