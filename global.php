<?php
/* =====================================================================
   BBMTANK OLYMPOS — ORTAK BASLANGIC
   ---------------------------------------------------------------------
   24.08.2026: SQL kullanici adi/sifresi bu dosyada ACIK yaziyordu ve
   dosya paylasilabilir hale gelince dogrudan sizacakti. Kimlik bilgileri
   gizli.php'ye tasindi; o dosya versiyon kontrolune girmiyor.
   Yeni kurulumda: gizli.ornek.php -> gizli.php kopyala, icini doldur.
   ===================================================================== */
@session_start();
@ini_set('display_errors', '0');
error_reporting(E_ERROR | E_PARSE);

require_once __DIR__ . '/gizli_yukle.php';
$_gizli = bbm_gizli_yukle(__DIR__);
if ($_gizli === null) {
    die('Yapilandirma eksik: gizli.ornek.php dosyasini gizli.php olarak kopyalayip doldurun '
        . '(tercihen site klasorunun BIR USTUNE).');
}
require_once $_gizli;

# --- baglanti ---
$conn                   = null;
$c_host                 = $BBM_SQL_HOST;
$config['UID']          = $BBM_SQL_UID;
$config['PWD']          = $BBM_SQL_PWD;
$config['Database']     = $BBM_DB_UYELIK;
$config['CharacterSet'] = 'UTF-8';   # degistirme
$dbtank                 = $BBM_DB_OYUN;

# --- sunucu baglantilari ---
$LinkLogin['Servidor 1'] = 'http://bbmtank.com/';
$LinkLogin['Servidor 2'] = 'http://bbmtank.com/';
$LinkFlash['br']         = 'http://bbmtank.com/335flash/';
$LinkFlash['vt']         = 'http://res124.gn.zing.vn/flashtr/';

# --- oyun dosyalari ---
# 24.08.2026: $Play[1] sunucuda BULUNMAYAN playvip.php'yi gosteriyordu.
# Reklamsiz/VIP ayrimi bu kurulumda yok; ikisi de tek launcher'a bakar.
$Play[0] = 'play.php';
$Play[1] = 'play.php';
$RateTimeToCoin = 5;   # cevrimici sureden kupona cevrim orani

include('function.php');
