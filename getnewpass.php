<?php
/* =====================================================================
   BBMTANK OLYMPOS — SIFRE KURTARMA (24.08.2026)
   ---------------------------------------------------------------------
   Eski dosya calismiyordu: kendi sendmail() fonksiyonunu tanimliyor,
   function.php de ayni adla bir tane daha tanimliyordu ->
   "Cannot redeclare sendmail()" -> sayfa HTTP 500 veriyordu. Yani
   "Sifremi unuttum" hic calismamis.
   Ayrica: Portekizce metinler, baska bir sunucuya (pvptank.tk) giden
   linkler ve e-postada DUZ METIN olarak gonderilen canta sifresi vardi.
   Hepsi duzeltildi; SMTP bilgileri gizli.php'ye tasindi.
   ===================================================================== */
ini_set('default_charset', 'UTF-8');
include('global.php');

if (isset($_SESSION['UserId'])) {
    echo '<script>window.location="index.php";</script>';
    exit();
}

$mesaj = '';
$tur   = '';   /* hata | basari */

function _smtpVar() {
    return isset($GLOBALS['BBM_SMTP_HOST']) && $GLOBALS['BBM_SMTP_HOST'] !== ''
        && isset($GLOBALS['BBM_SMTP_USER']) && $GLOBALS['BBM_SMTP_USER'] !== '';
}

function bbmPostaGonder($alici, $ad, $konu, $govde) {
    if (!_smtpVar()) return false;
    require_once __DIR__ . '/PhpMailer/class.phpmailer.php';
    $m = new PHPMailer();
    $m->IsSMTP();
    $m->SMTPDebug   = 0;
    $m->Host        = $GLOBALS['BBM_SMTP_HOST'];
    $m->Port        = (int)$GLOBALS['BBM_SMTP_PORT'];
    $m->SMTPSecure  = $GLOBALS['BBM_SMTP_GUVENLIK'];
    $m->SMTPAuth    = true;
    $m->Username    = $GLOBALS['BBM_SMTP_USER'];
    $m->Password    = $GLOBALS['BBM_SMTP_PASS'];
    $m->SetFrom($GLOBALS['BBM_SMTP_GONDEREN'], 'BBMTank Olympos');
    $m->AddAddress($alici, $ad);
    $m->CharSet = 'utf-8';
    $m->Subject = $konu;
    $m->MsgHTML($govde);
    $m->AltBody = strip_tags($govde);
    return $m->Send();
}

function _site() {
    $h = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'bbmtank.com';
    return 'http://' . $h;
}

co();

/* ---------------- 1) kurtarma talebi ---------------- */
if (isset($_POST['istek'])) {
    $u = EscapeString(trim($_POST['kullanici']));
    $e = EscapeString(trim($_POST['eposta']));

    if ($u === '' || $e === '') {
        $mesaj = 'Kullanıcı adı ve e-posta gerekiyor.'; $tur = 'hata';
    } elseif (qn(q("SELECT TOP 1 UserId FROM Webshop_Account WHERE Email = '{$e}' AND UserName = '{$u}'")) != 1) {
        $mesaj = 'Bu kullanıcı adı ve e-posta eşleşmiyor.'; $tur = 'hata';
    } else {
        $bekleyen = qa(q("SELECT COUNT(ID) AS a FROM Log_GetPass WHERE UserName = '{$u}' AND IsChange = 'False'"));
        if ($bekleyen && (int)$bekleyen['a'] > 0) {
            $mesaj = 'Zaten bekleyen bir sıfırlama isteğin var. Gelen kutunu ve spam klasörünü kontrol et.';
            $tur = 'hata';
        } elseif (!_smtpVar()) {
            $mesaj = 'Posta gönderimi bu sunucuda yapılandırılmamış. Discord üzerinden karakter adınla yaz, '
                   . 'şifreni elle sıfırlayalım.';
            $tur = 'hata';
        } else {
            $zaman = gmdate('Y-m-d H:i:s');
            $kod   = md5($u . $zaman . mt_rand());
            q("INSERT INTO [dbo].[Log_GetPass] ([UserName],[Code],[Time],[IsChange]) "
              . "VALUES ('{$u}','{$kod}','{$zaman}','False')");

            $bag = _site() . '/getnewpass.php?check=True&u=' . rawurlencode($u) . '&code=' . rawurlencode($kod);
            $govde = '<div style="font-family:Segoe UI,Arial,sans-serif;color:#2a1d0c">'
              . '<h2 style="color:#8a5a1e">Merhaba ' . htmlspecialchars($u) . ',</h2>'
              . '<p>BBMTank Olympos hesabın için şifre sıfırlama isteği aldık.</p>'
              . '<p><a href="' . $bag . '" style="display:inline-block;padding:12px 22px;border-radius:8px;'
              . 'background:#d4a251;color:#2a1d0c;font-weight:bold;text-decoration:none">Şifremi sıfırla</a></p>'
              . '<p style="font-size:13px;color:#666">Bağlantı çalışmazsa şunu tarayıcına yapıştır:<br>'
              . '<a href="' . $bag . '">' . $bag . '</a></p>'
              . '<p style="font-size:13px;color:#666">Bu isteği sen yapmadıysan bu e-postayı yok sayabilirsin; '
              . 'şifren değişmez.</p></div>';

            if (bbmPostaGonder($e, $u, 'BBMTank Olympos — şifre sıfırlama', $govde)) {
                $mesaj = 'Sıfırlama bağlantısı e-postana gönderildi. Gelen kutunda yoksa spam klasörüne bak.';
                $tur = 'basari';
            } else {
                $mesaj = 'E-posta gönderilemedi. Discord üzerinden bize yaz, elle halledelim.';
                $tur = 'hata';
            }
        }
    }
}

/* ---------------- 2) baglantiya tiklandi ---------------- */
$yeniSifre = '';
if (isset($_GET['check'])) {
    $u    = EscapeString($_GET['u']);
    $kod  = EscapeString($_GET['code']);
    if (qn(q("SELECT TOP 1 ID FROM Log_GetPass WHERE UserName = '{$u}' AND Code = '{$kod}' AND IsChange = 'False'")) == 1) {
        q("UPDATE Log_GetPass SET IsChange = 'True' WHERE UserName = '{$u}' AND Code = '{$kod}'");
        $yeniSifre = '';
        $havuz = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
        for ($i = 0; $i < 10; $i++) $yeniSifre .= $havuz[mt_rand(0, strlen($havuz) - 1)];
        $bilgi = qa(q("SELECT TOP 1 UserId FROM Mem_Users WHERE UserName = '{$u}'"));
        if ($bilgi) {
            q("UPDATE Mem_UserInfo SET Password = '" . strtoupper(md5($yeniSifre)) . "' WHERE UserId = '" . (int)$bilgi['UserId'] . "'");
            $mesaj = 'Şifren değiştirildi.'; $tur = 'basari';
        } else {
            $yeniSifre = ''; $mesaj = 'Hesap bulunamadı.'; $tur = 'hata';
        }
    } else {
        $mesaj = 'Bu sıfırlama bağlantısı geçersiz ya da daha önce kullanılmış.'; $tur = 'hata';
    }
}

$sayfaBaslik   = 'Şifre Kurtarma';
$sayfaAciklama = 'BBMTank Olympos hesabının şifresini e-posta ile sıfırla.';
include('parts/ust.php');
?>

<section class="bolum" style="padding-top:56px">
  <div class="wrap">

    <div class="baslik rv">
      <div class="tepe">Yardım masası</div>
      <h1>Şifreni <span class="vurgu">kurtaralım</span></h1>
      <p>Kayıt olurken verdiğin kullanıcı adı ve e-posta ile yeni bir şifre alabilirsin.</p>
    </div>

    <div class="izgara i2" style="align-items:start">

      <div class="levha parsomen rv">
        <span class="flama altin">Sıfırlama</span>
        <h3>Bilgilerini gir</h3>

        <?php if ($mesaj): ?>
          <div class="<?php echo $tur === 'basari' ? 'basari' : 'hata'; ?>"><?php echo htmlspecialchars($mesaj); ?></div>
        <?php endif; ?>

        <?php if ($yeniSifre): ?>
          <p style="margin:6px 0 4px;font-size:14px;color:#6b5735">Yeni şifren:</p>
          <div style="padding:14px 16px;border-radius:10px;border:2px solid var(--altin-golge);
                      background:rgba(255,255,255,.9);font-family:var(--olcek);font-size:26px;
                      font-weight:900;letter-spacing:2px;text-align:center;color:var(--altin-derin)">
            <?php echo htmlspecialchars($yeniSifre); ?>
          </div>
          <p style="margin-top:12px;font-size:13.5px;color:#6b5735">
            Bu şifreyi bir yere not et, sayfayı yenileyince tekrar gösterilmez.
            Giriş yaptıktan sonra değiştirmeni öneririz.
          </p>
          <div style="margin-top:16px"><a class="btn" href="Anasayfa.php#giris">Giriş Yap</a></div>
        <?php else: ?>
          <form action="getnewpass.php" method="POST" autocomplete="off" style="margin-top:12px">
            <div class="alan">
              <label for="k1">Kullanıcı Adı</label>
              <input id="k1" type="text" name="kullanici" required placeholder="giriş yaparken kullandığın ad">
            </div>
            <div class="alan">
              <label for="k2">E-posta</label>
              <input id="k2" type="email" name="eposta" required placeholder="kayıt olurken verdiğin adres">
            </div>
            <button class="btn" type="submit" name="istek" style="width:100%;justify-content:center">
              Sıfırlama Bağlantısı Gönder
            </button>
          </form>
        <?php endif; ?>
      </div>

      <div>
        <div class="levha rv" style="margin-bottom:22px">
          <span class="flama mavi">E-postanı hatırlamıyorsan</span>
          <h3>Discord'a yaz</h3>
          <p>
            Karakter adını, yaklaşık kayıt tarihini ve hatırladığın son giriş bilgilerini yaz;
            hesabı elle doğrulayıp şifreni sıfırlayalım.
          </p>
          <div style="margin-top:16px">
            <a class="btn mavi" href="https://discord.gg/xbVEbfhVbw" target="_blank" rel="noopener">Discord Sunucusu</a>
          </div>
        </div>
        <div class="levha parsomen rv">
          <span class="flama">Güvenlik</span>
          <h3>Çanta şifresi ayrıdır</h3>
          <p>
            Buradan yalnızca <b>giriş şifresi</b> sıfırlanır. Oyun içi kasanı açan çanta şifresi
            hiçbir e-postada gönderilmez; onu unuttuysan Discord'dan destek al.
          </p>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include('parts/alt.php'); ?>
