<?php
/* =====================================================================
   BBMTANK OLYMPOS — GIZLI YAPILANDIRMA YUKLEYICI     24.08.2026
   ---------------------------------------------------------------------
   gizli.php iki yerden birinde olabilir:
     1) SITE KLASORUNUN BIR USTU  (../gizli.php)  -> ONERILEN
        Web kokunun disinda kaldigi icin, PHP isleyicisi bir sekilde
        devre disi kalsa bile dosya tarayiciya dumduz metin olarak
        servis edilemez.
     2) Site klasorunun icinde (./gizli.php)      -> basit kurulum
   Bu dosya sirayla ikisine bakar. Icinde hicbir gizli bilgi yoktur.
   ===================================================================== */
if (!function_exists('bbm_gizli_yukle')) {
    function bbm_gizli_yukle($siteKlasoru) {
        foreach (array($siteKlasoru . '/../gizli.php', $siteKlasoru . '/gizli.php') as $yol) {
            if (file_exists($yol)) { return $yol; }
        }
        return null;
    }
}
