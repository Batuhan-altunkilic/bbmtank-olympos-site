<?php
/* =====================================================================
   BBMTANK OLYMPOS — KAYIT (24.08.2026)
   ---------------------------------------------------------------------
   Eski register.php devralinan kaynaktan geliyordu. Duzeltilenler:
     1) GUVENLIK: her kayitta calisan
        "Update Sys_Users_Detail set Equipe = 4 Where UserName = 'Paulo157'"
        satiri vardi. Paulo157 adiyla kayit olan herkes yonetici (Equipe=4)
        oluyordu. Kaldirildi.
     2) "update Sys_Users_Extra set MissionEnergy = 10000000 where UserID = '{$uid}'"
        satirindaki $uid hic tanimli degildi (islem bos gidiyordu) ve deger
        zaten 10 milyon enerji gibi bir hata ayiklama degeriydi. Kaldirildi;
        enerji artik prosedurun kendi varsayilaniyla basliyor.
     3) Ic ice iki <form> vardi (biri hic kapanmiyordu) -> tarayicida ikinci
        form yok sayiliyordu. Tek forma indirildi.
     4) @passtwo icin $p2 hic toplanmiyordu; md5(null) yaziliyordu. Artik
        "Canta Sifresi" alani var.
     5) Portekizce artiklar ve alert() yigini gitti; hatalar sayfada.
   ===================================================================== */
ini_set('default_charset', 'UTF-8');
include('global.php');

if (isset($_SESSION['UserId'])) {
    echo '<script>window.location="index.php";</script>';
    exit();
}

$hatalar = array();
$basari  = '';
$eski    = array('rusername' => '', 'nickname' => '', 'email' => '', 'sex' => '1');

if (isset($_POST['register'])) {
    $u  = trim($_POST['rusername']);
    $n  = trim($_POST['nickname']);
    $e  = trim($_POST['email']);
    $p  = $_POST['rpassword'];
    $rp = $_POST['rtpassword'];
    $p2 = $_POST['canta'];
    $s  = (int)$_POST['sex'];

    $eski['rusername'] = $u; $eski['nickname'] = $n;
    $eski['email'] = $e;     $eski['sex'] = (string)$s;

    if ($u === '' || $n === '' || $e === '' || $p === '' || $rp === '' || $p2 === '')
        $hatalar[] = 'Tüm alanları doldurman gerekiyor.';
    if (!preg_match('/^([a-zA-Z0-9\-\_]*)$/', $u))
        $hatalar[] = 'Kullanıcı adı yalnızca harf, rakam, tire ve alt çizgi içerebilir.';
    if (!preg_match('/^([a-zA-Z0-9\-\_]*)$/', $n))
        $hatalar[] = 'Karakter adı yalnızca harf, rakam, tire ve alt çizgi içerebilir.';
    if (!filter_var($e, FILTER_VALIDATE_EMAIL))
        $hatalar[] = 'E-posta adresi geçerli görünmüyor.';
    if ($p !== $rp)
        $hatalar[] = 'İki şifre birbirini tutmuyor.';
    if (strlen($u) < 6 || strlen($u) > 30)
        $hatalar[] = 'Kullanıcı adı 6-30 karakter olmalı.';
    if (strlen($p) < 6 || strlen($p) > 30)
        $hatalar[] = 'Şifre 6-30 karakter olmalı.';
    if (strlen($p2) < 6 || strlen($p2) > 30)
        $hatalar[] = 'Çanta şifresi 6-30 karakter olmalı.';
    if (strlen($n) < 6 || strlen($n) > 30)
        $hatalar[] = 'Karakter adı 6-30 karakter olmalı.';

    $kucuk = strtolower($n);
    foreach (array('gm', 'adm', 'mod', 'gunny', 'game master') as $yasak) {
        if (strpos($kucuk, $yasak) !== false) {
            $hatalar[] = 'Karakter adında "GM", "ADM", "MOD" gibi yetkili ifadeleri kullanamazsın.';
            break;
        }
    }

    if (!$hatalar) {
        co();
        $us = addslashes($u); $ns = addslashes($n); $es = addslashes($e);
        $ph = strtoupper(md5($p));

        if (qn(q("SELECT TOP 1 UserId FROM Mem_Users WHERE UserName = '{$us}'")) != 0) {
            $hatalar[] = 'Bu kullanıcı adı zaten alınmış.';
        } elseif (qn(q("SELECT TOP 1 UserId FROM Webshop_Account WHERE Email = '{$es}'")) != 0) {
            $hatalar[] = 'Bu e-posta adresiyle daha önce kayıt olunmuş.';
        } elseif (qn(q("SELECT TOP 1 UserId FROM {$dbtank}.dbo.Sys_Users_Detail WHERE NickName = '{$ns}'")) != 0) {
            $hatalar[] = 'Bu karakter adı kullanılıyor, başka bir tane dene.';
        } else {
            q("exec " . $config['Database'] . ".dbo.Webshop_Register @ApplicationName=N'DanDanTang',"
              . "@UserName=N'{$us}',@password=N'{$ph}',@email='{$es}',"
              . "@passtwo = '" . strtoupper(md5($p2)) . "',@error = 0");

            /* BBMTank ROADMAP P0-1 (2026-08-20): @GP/@Grade prosedure birakildi.
               SP_Users_Active baslangic seviyesini BBM_Calibration.NewPlayerStartGrade
               anahtarindan okur; buradaki 0'lar sadece imza doldurmasidir. */
            q("exec {$dbtank}.dbo.SP_Users_Active @UserID='',@Attack=0,@Colors=N',,,,,,',@ConsortiaID=0,"
              . "@Defence=0,@Gold=0,@GP=0,@Grade=0,@Luck=0,@Money=0,@Style=N',,,,,,',@Agility=0,@State=0,"
              . "@UserName=N'{$us}',@PassWord=N'{$ph}',@Sex='{$s}',@Hide=1111111111,@ActiveIP=N'',@Skin=N'',@Site=N''");

            if ($s == 1) {
                q("exec {$dbtank}.dbo.SP_Users_RegisterNotValidate @UserName=N'{$us}',@PassWord=N'{$ph}',"
                  . "@NickName=N'{$ns}',@BArmID=7003,@BHairID=3158,@BFaceID=6103,@BClothID=5160,@BHatID=1142,"
                  . "@GArmID=7003,@GHairID=3158,@GFaceID=6103,@GClothID=5160,@GHatID=1142,@ArmColor=N'',"
                  . "@HairColor=N'',@FaceColor=N'',@ClothColor=N'',@HatColor=N'',@Sex='{$s}',@StyleDate=0");
            } else {
                q("exec {$dbtank}.dbo.SP_Users_RegisterNotValidate @UserName=N'{$us}',@PassWord=N'{$ph}',"
                  . "@NickName=N'{$ns}',@BArmID=7003,@BHairID=3244,@BFaceID=6204,@BClothID=5276,@BHatID=1214,"
                  . "@GArmID=7003,@GHairID=3244,@GFaceID=6202,@GClothID=5276,@GHatID=1214,@ArmColor=N'',"
                  . "@HairColor=N'',@FaceColor=N'',@ClothColor=N'',@HatColor=N'',@Sex='{$s}',@StyleDate=0");
            }
            q("exec {$dbtank}.dbo.SP_Users_LoginWeb @UserName=N'{$us}',@Password=N'',@FirstValidate=0,@NickName=N'{$ns}'");

            $basari = 'Hoş geldin ' . htmlspecialchars($n) . '! Hesabın hazır, hemen giriş yapabilirsin.';
            $eski = array('rusername' => '', 'nickname' => '', 'email' => '', 'sex' => '1');
        }
    }
}

$sayfaBaslik   = 'Kayıt Ol';
$sayfaAciklama = 'BBMTank Olympos ücretsiz hesap oluştur. Kurulum yok, doğrulama beklemesi yok; kayıt ol ve tarayıcıdan savaşa gir.';
$sayfaAnahtar  = 'bbmtank kayıt, ücretsiz hesap, olympos üye ol';
include('parts/ust.php');
?>

<section class="bolum" style="padding-top:56px">
  <div class="wrap">

    <div class="baslik rv">
      <div class="tepe">Ücretsiz kayıt</div>
      <h1>Otuz saniye sonra <span class="vurgu">meydandasın</span></h1>
      <p>
        E-posta doğrulaması yok, bekleme yok, indirme yok. Formu doldur, giriş yap,
        ilk atışını yap.
      </p>
    </div>

    <div class="izgara i2" style="align-items:start">

      <div class="levha parsomen rv">
        <span class="flama altin">Hesap</span>
        <h3>Bilgilerin</h3>

        <?php if ($basari): ?>
          <div class="basari"><?php echo $basari; ?></div>
          <div style="margin-top:14px"><a class="btn" href="Anasayfa.php#giris">Giriş Yap</a></div>
        <?php endif; ?>

        <?php if ($hatalar): ?>
          <div class="hata">
            <?php foreach ($hatalar as $h) echo '&bull; ' . htmlspecialchars($h) . '<br>'; ?>
          </div>
        <?php endif; ?>

        <form action="register.php" method="POST" autocomplete="off" style="margin-top:12px">
          <div class="alan">
            <label for="a1">Kullanıcı Adı</label>
            <input id="a1" type="text" name="rusername" minlength="6" maxlength="30" required
                   value="<?php echo htmlspecialchars($eski['rusername']); ?>"
                   placeholder="giriş yaparken kullanacağın ad">
          </div>
          <div class="alan">
            <label for="a2">Karakter Adı</label>
            <input id="a2" type="text" name="nickname" minlength="6" maxlength="30" required
                   value="<?php echo htmlspecialchars($eski['nickname']); ?>"
                   placeholder="oyunda görünecek ad">
          </div>
          <div class="alan">
            <label for="a3">E-posta</label>
            <input id="a3" type="email" name="email" required
                   value="<?php echo htmlspecialchars($eski['email']); ?>"
                   placeholder="şifreni unutursan buradan kurtarırsın">
          </div>
          <div class="alan">
            <label for="a4">Şifre</label>
            <input id="a4" type="password" name="rpassword" minlength="6" maxlength="30" required placeholder="••••••••">
          </div>
          <div class="alan">
            <label for="a5">Şifre Tekrar</label>
            <input id="a5" type="password" name="rtpassword" minlength="6" maxlength="30" required placeholder="••••••••">
          </div>
          <div class="alan">
            <label for="a6">Çanta Şifresi</label>
            <input id="a6" type="password" name="canta" minlength="6" maxlength="30" required placeholder="oyun içi kasa şifresi">
          </div>
          <div class="alan">
            <label for="a7">Cinsiyet</label>
            <select id="a7" name="sex">
              <option value="1"<?php echo $eski['sex'] === '1' ? ' selected' : ''; ?>>Erkek</option>
              <option value="0"<?php echo $eski['sex'] === '0' ? ' selected' : ''; ?>>Kadın</option>
            </select>
          </div>
          <button class="btn" type="submit" name="register" style="width:100%;justify-content:center">
            Hesabımı Oluştur
          </button>
        </form>

        <p style="margin-top:14px;font-size:13px;color:#6b5735">
          Kaydolarak sunucu kurallarını kabul etmiş olursun. Hile, çoklu hesap istismarı ve
          oyuncu taciziyle ilgili kurallar Discord'da yazılı.
        </p>
      </div>

      <div>
        <div class="levha rv" style="margin-bottom:22px">
          <span class="flama mavi">Bilmen gereken</span>
          <h3>Karakter adı kalıcıdır</h3>
          <p>
            Oyunda görünen ad karakter adıdır ve sonradan yalnızca <b>isim değiştirme kartı</b>
            ile değişir. Kullanıcı adı ise hesabın kimliğidir, hiç değişmez. İkisini karıştırma.
          </p>
        </div>
        <div class="levha rv" style="margin-bottom:22px">
          <span class="flama">Çanta şifresi ne?</span>
          <h3>İkinci bir kilit</h3>
          <p>
            Oyun içi kasanı açarken sorulan ayrı şifredir. Giriş şifrenden farklı olsun ki
            hesabın ele geçse bile eşyaların korunsun.
          </p>
        </div>
        <div class="levha parsomen rv">
          <span class="flama altin">Sonraki adım</span>
          <h3>Kayıttan sonra</h3>
          <a class="haber" href="rehber.php#savas"><span class="tar">1</span> Açı, güç ve rüzgârı öğren</a>
          <a class="haber" href="kesifler.php"><span class="tar">2</span> Seviyene uygun keşfe gir</a>
          <a class="haber" href="rehber.php#pazar"><span class="tar">3</span> Düşeni pazarda sat</a>
          <a class="haber" href="rehber.php#otoav"><span class="tar">4</span> Keşfi otomatik ava bırak</a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include('parts/alt.php'); ?>
