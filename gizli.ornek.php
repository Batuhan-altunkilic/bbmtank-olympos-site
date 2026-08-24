<?php
/* =====================================================================
   BBMTANK OLYMPOS — GIZLI YAPILANDIRMA SABLONU
   ---------------------------------------------------------------------
   KULLANIM:
     1) Bu dosyanin bir kopyasini ayni klasore "gizli.php" adiyla al.
     2) Asagidaki degerleri kendi sunucuna gore doldur.
     3) gizli.php'yi ASLA depoya ekleme (.gitignore zaten disliyor).
   ===================================================================== */

/* --- SQL Server --- */
$BBM_SQL_HOST = '.\SQLEXPRESS';   /* orn: .\GUNNY, 127.0.0.1, localhost\SQL2019 */
$BBM_SQL_UID  = 'sa';
$BBM_SQL_PWD  = 'BURAYA_SIFRE';

/* --- veritabani adlari --- */
$BBM_DB_UYELIK = 'BBM_Membership';
$BBM_DB_OYUN   = 'BBM_Tank';

/* --- e-posta (sifre kurtarma) ---
   Bos birakirsan sifre kurtarma sayfasi kullaniciyi Discord'a yonlendirir. */
$BBM_SMTP_HOST     = 'smtp.ornek.com';
$BBM_SMTP_PORT     = 587;
$BBM_SMTP_GUVENLIK = 'tls';
$BBM_SMTP_USER     = 'destek@ornek.com';
$BBM_SMTP_PASS     = 'BURAYA_POSTA_SIFRESI';
$BBM_SMTP_GONDEREN = 'destek@ornek.com';

/* --- yonetici paneli --- */
$BBM_PANEL_PASS = 'BURAYA_UZUN_RASTGELE_BIR_SIFRE';

/* --- yedek SQL sunucusu (istege bagli) --- */
$BBM_SQL2_HOST = '';
$BBM_SQL2_UID  = '';
$BBM_SQL2_PWD  = '';
