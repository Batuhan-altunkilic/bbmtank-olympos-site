<?php
/* =====================================================================
   BBMTANK OLYMPOS — login.php                     24.08.2026
   ---------------------------------------------------------------------
   Bu dosya bir "dogrulama" ara sayfasiydi ama hicbir sey dogrulamiyordu:
     * Sayfada bir reCAPTCHA kutusu vardi, fakat sunucu tarafinda
       g-recaptcha-response HIC kontrol edilmiyordu -> tamamen dekoratif.
     * Form, Anasayfa.php'ye kullanici adi/sifre olmadan BOS POST atiyordu;
       giris diye bir sey gerceklesmiyordu.
   Gercek giris formu Anasayfa.php'de. Bu dosya artik oraya yonlendiriyor;
   eski baglantilar (ornegin ajax.php) kirilmasin diye duruyor.
   ===================================================================== */
header('Location: Anasayfa.php#giris', true, 301);
exit();
