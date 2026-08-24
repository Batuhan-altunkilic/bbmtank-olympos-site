<?php
/* =====================================================================
   BBMTANK OLYMPOS — SIFRE KURTARMA               24.08.2026
   ---------------------------------------------------------------------
   Once: dosya "Cannot redeclare sendmail()" ile HTTP 500 veriyordu,
   yani "Sifremi unuttum" hic calismamisti.

   Bu turda kapatilanlar:
     1) EscapeString() girdiden TIRE siliyordu. register.php kullanici
        adinda tireye izin verdigi icin "bat-han" gibi kullanicilar ve
        tireli e-postalar veritabaniyla ASLA eslesmiyordu -> o oyuncular
        sifresini hic kurtaramazdi. Artik girdi bozulmuyor, sorgular
        parametreli (qp).
     2) Sifirlama kodu ve yeni sifre mt_rand() ile uretiliyordu (tahmin
        edilebilir). random_bytes / random_int'e gecildi.
     3) Log_GetPass.Time yaziliyor ama hic okunmuyordu -> sifirlama
        baglantisi SURESIZ gecerliydi. Artik 1 saat.
     4) Bekleyen istek kontrolu sureyi hesaba katmiyordu; suresi gecmis
        tek bir istek oyuncuyu sonsuza kadar kilitliyordu.
     5) PHPMailer iki farkli yoldan isteniyordu (PHPMailer/ ve PhpMailer/);
        buyuk-kucuk harf duyarli bir sistemde biri kesin patlardi. Tek
        yol + dosya varlik kontrolu.
     6) Forma CSRF jetonu eklendi.
   ===================================================================== */
ini_set('default_charset', 'UTF-8');
include('global.php');

if (isset($_SESSION['UserId'])) {
    header('Location: index.php');
    exit();
}

define('BBM_SIFIRLAMA_OMRU', 3600);          /* saniye: 1 saat */

$mesaj = '';
$tur   = '';        /* hata | basari */
$yeniSifre = '';

function _smtpVar() {
    return !empty($GLOBALS['BBM_SMTP_HOST']) && !empty($GLOBALS['BBM_SMTP_USER']);
}

function bbmPostaGonder($alici, $ad, $konu, $govde) {
    if (!_smtpVar()) return false;
    $yukleyici = __DIR__ . '/PHPMailer/PHPMailerAutoload.php';
    if (!file_exists($yukleyici)) return false;      /* kutuphane yoksa sessizce basarisiz */
    require_once $yukleyici;
    $m = new PHPMailer();
    $m->IsSMTP();
    $m->SMTPDebug  = 0;
    $m->Host       = $GLOBALS['BBM_SMTP_HOST'];
    $m->Port       = (int)$GLOBALS['BBM_SMTP_PORT'];
    $m->SMTPSecure = $GLOBALS['BBM_SMTP_GUVENLIK'];
    $m->SMTPAuth   = true;
    $m->Username   = $GLOBALS['BBM_SMTP_USER'];
    $m->Password   = $GLOBALS['BBM_SMTP_PASS'];
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

/* rastgele, tahmin edilemez dize */
function _rastgele($uzunluk, $havuz = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789') {
    $son = '';
    $n = strlen($havuz) - 1;
    for ($i = 0; $i < $uzunluk; $i++) $son .= $havuz[random_int(0, $n)];
    return $son;
}

co();

/* ---------------- 1) kurtarma talebi ---------------- */
if (isset($_POST['istek'])) {
    if (!csrfGecerli()) {
        $mesaj = 'Oturum doğrulaması başarısız. Sayfayı yenileyip tekrar dene.'; $tur = 'hata';
    } else {
        $u = trim((string)$_POST['kullanici']);
        $e = trim((string)$_POST['eposta']);

        if ($u === '' || $e === '') {
            $mesaj = 'Kullanıcı adı ve e-posta gerekiyor.'; $tur = 'hata';
        } elseif (!qp1("SELECT TOP 1 UserId FROM Webshop_Account WHERE Email = ? AND UserName = ?", array($e, $u))) {
            $mesaj = 'Bu kullanıcı adı ve e-posta eşleşmiyor.'; $tur = 'hata';
        } else {
            /* Suresi DOLMAMIS bekleyen istek var mi? (eskiden sure hic bakilmiyordu) */
            $bekliyor = false;
            $rs = qp("SELECT [Time] FROM Log_GetPass WHERE UserName = ? AND IsChange = 0", array($u));
            if ($rs) {
                while ($x = qa($rs)) {
                    $t = strtotime($x['Time'] . ' UTC');
                    if ($t && (time() - $t) < BBM_SIFIRLAMA_OMRU) { $bekliyor = true; break; }
                }
            }

            if ($bekliyor) {
                $mesaj = 'Zaten bekleyen bir sıfırlama isteğin var. Gelen kutunu ve spam klasörünü kontrol et. '
                       . 'Bağlantı 1 saat geçerlidir.';
                $tur = 'hata';
            } elseif (!_smtpVar()) {
                $mesaj = 'Posta gönderimi bu sunucuda yapılandırılmamış. Discord üzerinden karakter adınla yaz, '
                       . 'şifreni elle sıfırlayalım.';
                $tur = 'hata';
            } else {
                $zaman = gmdate('Y-m-d H:i:s');
                $kod   = bin2hex(random_bytes(16));      /* 32 karakter, tahmin edilemez */
                qp("INSERT INTO [dbo].[Log_GetPass] ([UserName],[Code],[Time],[IsChange]) VALUES (?,?,?,0)",
                   array($u, $kod, $zaman));

                $bag = _site() . '/getnewpass.php?check=True&u=' . rawurlencode($u) . '&code=' . rawurlencode($kod);
                $govdeHtml = '<div style="font-family:Segoe UI,Arial,sans-serif;color:#2a1d0c">'
                  . '<h2 style="color:#8a5a1e">Merhaba ' . htmlspecialchars($u) . ',</h2>'
                  . '<p>BBMTank Olympos hesabın için şifre sıfırlama isteği aldık.</p>'
                  . '<p><a href="' . $bag . '" style="display:inline-block;padding:12px 22px;border-radius:8px;'
                  . 'background:#d4a251;color:#2a1d0c;font-weight:bold;text-decoration:none">Şifremi sıfırla</a></p>'
                  . '<p style="font-size:13px;color:#666">Bağlantı <b>1 saat</b> geçerlidir. Çalışmazsa şunu '
                  . 'tarayıcına yapıştır:<br><a href="' . $bag . '">' . $bag . '</a></p>'
                  . '<p style="font-size:13px;color:#666">Bu isteği sen yapmadıysan bu e-postayı yok sayabilirsin; '
                  . 'şifren değişmez.</p></div>';

                if (bbmPostaGonder($e, $u, 'BBMTank Olympos — şifre sıfırlama', $govdeHtml)) {
                    $mesaj = 'Sıfırlama bağlantısı e-postana gönderildi (1 saat geçerli). '
                           . 'Gelen kutunda yoksa spam klasörüne bak.';
                    $tur = 'basari';
                } else {
                    /* gonderilemedi -> kaydi kilitli birakma */
                    qp("UPDATE Log_GetPass SET IsChange = 1 WHERE UserName = ? AND Code = ?", array($u, $kod));
                    $mesaj = 'E-posta gönderilemedi. Discord üzerinden bize yaz, elle halledelim.';
                    $tur = 'hata';
                }
            }
        }
    }
}

/* ---------------- 2) baglantiya tiklandi ---------------- */
if (isset($_GET['check'])) {
    $u   = (string)$_GET['u'];
    $kod = (string)$_GET['code'];

    $kayit = qp1("SELECT TOP 1 ID, [Time] FROM Log_GetPass WHERE UserName = ? AND Code = ? AND IsChange = 0",
                 array($u, $kod));

    if (!$kayit) {
        $mesaj = 'Bu sıfırlama bağlantısı geçersiz ya da daha önce kullanılmış.'; $tur = 'hata';
    } else {
        $t = strtotime($kayit['Time'] . ' UTC');
        if (!$t || (time() - $t) >= BBM_SIFIRLAMA_OMRU) {
            qp("UPDATE Log_GetPass SET IsChange = 1 WHERE ID = ?", array((int)$kayit['ID']));
            $mesaj = 'Bu bağlantının süresi dolmuş (1 saat). Yeni bir sıfırlama isteği oluştur.'; $tur = 'hata';
        } else {
            $hesap = qp1("SELECT TOP 1 UserId FROM Mem_Users WHERE UserName = ?", array($u));
            if (!$hesap) {
                $mesaj = 'Hesap bulunamadı.'; $tur = 'hata';
            } else {
                $yeniSifre = _rastgele(10);
                qp("UPDATE Mem_UserInfo SET Password = ? WHERE UserId = ?",
                   array(strtoupper(md5($yeniSifre)), (int)$hesap['UserId']));
                qp("UPDATE Log_GetPass SET IsChange = 1 WHERE ID = ?", array((int)$kayit['ID']));
                $mesaj = 'Şifren değiştirildi.'; $tur = 'basari';
            }
        }
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
            <?php echo csrfAlan(); ?>
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
