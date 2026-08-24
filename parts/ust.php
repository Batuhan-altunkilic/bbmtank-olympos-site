<?php
/* =====================================================================
   BBMTANK OLYMPOS — ORTAK UST KABUK v2 (24.08.2026)
   Sayfanin en ustunde:
     $sayfaBaslik = '...'; $sayfaAciklama = '...';
     include('parts/ust.php');
   ===================================================================== */
if (!isset($sayfaBaslik))   $sayfaBaslik   = 'BBMTank Olympos';
if (!isset($sayfaAciklama)) $sayfaAciklama = 'Sıra tabanlı topçu savaşları. Açıyı ayarla, gücü tuttur, rüzgârı hesapla. Tarayıcıdan oynanır, kurulum yok.';
if (!isset($sayfaAnahtar))  $sayfaAnahtar  = 'bbmtank, olympos, tank oyunu, sıra tabanlı, topçu, türkçe mmo';
$_k = 'http://bbmtank.com/' . basename($_SERVER['PHP_SELF']);
$_menu = array(
  'Anasayfa.php' => 'Ana Sayfa',
  'rehber.php'   => 'Rehber',
  'kesifler.php' => 'Keşifler',
  'siralama.php' => 'Sıralama',
  'destek.php'   => 'Destek'
);
?><!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($sayfaBaslik); ?> | BBMTank Olympos</title>
<meta name="description" content="<?php echo htmlspecialchars($sayfaAciklama); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($sayfaAnahtar); ?>">
<link rel="canonical" href="<?php echo $_k; ?>">
<meta name="theme-color" content="#070c1a">
<meta name="robots" content="index,follow">
<meta property="og:type" content="website">
<meta property="og:site_name" content="BBMTank Olympos">
<meta property="og:locale" content="tr_TR">
<meta property="og:title" content="<?php echo htmlspecialchars($sayfaBaslik); ?> | BBMTank Olympos">
<meta property="og:description" content="<?php echo htmlspecialchars($sayfaAciklama); ?>">
<meta property="og:url" content="<?php echo $_k; ?>">
<meta property="og:image" content="http://bbmtank.com/assets/olympos/logo.png">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($sayfaBaslik); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($sayfaAciklama); ?>">
<meta name="twitter:image" content="http://bbmtank.com/assets/olympos/logo.png">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"BreadcrumbList","itemListElement":[
 {"@type":"ListItem","position":1,"name":"Ana Sayfa","item":"http://bbmtank.com/Anasayfa.php"},
 {"@type":"ListItem","position":2,"name":"<?php echo htmlspecialchars($sayfaBaslik); ?>","item":"<?php echo $_k; ?>"}]}
</script>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"VideoGame","name":"BBMTank Olympos",
 "genre":["Sıra tabanlı","Topçu","MMO"],"gamePlatform":"Web Tarayıcı","inLanguage":"tr",
 "url":"http://bbmtank.com/","description":"Açıyı ayarla, gücü tuttur, rüzgârı hesapla."}
</script>
<link rel="icon" type="image/png" href="assets/olympos/favicon.png">
<link rel="apple-touch-icon" href="assets/olympos/logo-sm.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/bbm.css?v=20260824h">
</head>
<body>
<div class="oly-zemin"></div>
<div class="oly-toz"></div>

<header class="oly-nav">
  <div class="ic">
    <a class="oly-marka" href="Anasayfa.php">
      <img src="assets/olympos/logo-sm.png" alt="BBMTank Olympos" width="52" height="52"
           loading="eager" fetchpriority="high" decoding="async">
      <b>BBMTANK</b>
    </a>
    <nav class="oly-baglar">
      <?php foreach ($_menu as $_d => $_a) {
        $_on = (strtolower(basename($_SERVER['PHP_SELF'])) === strtolower($_d)) ? ' class="on"' : '';
        echo '<a href="' . $_d . '"' . $_on . '>' . $_a . '</a>';
      } ?>
      <a class="btn" style="padding:11px 20px;font-size:14.5px" href="index.php">Hemen Oyna</a>
    </nav>
  </div>
</header>
