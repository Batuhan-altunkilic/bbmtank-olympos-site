
<?php
 ini_set('default_charset','UTF-8');
?>
<?php
include('global.php');
if(isset($_SESSION['UserId'])) {
	echo '<script type="text/javascript">window.location="index.php";</script>';
	exit();
}
$loginError = '';
if(isset($_POST['login'])) {
	co();
	$u = addslashes($_POST['username']);
	$p = strtoupper(md5($_POST['password']));
	$app  = 'DanDanTang';
	$uid  = 0;
	$data = array(
		array($app, SQLSRV_PARAM_IN),
		array($u, SQLSRV_PARAM_IN),
		array($p, SQLSRV_PARAM_IN),
		array(&$uid, SQLSRV_PARAM_OUT)
	);
	$check = sqlsrv_query($conn, "{CALL Mem_Users_Accede (?,?,?,?)}", $data);
	sqlsrv_next_result($check);
	if($uid <= 0)
	{
		$loginError = 'Kullanıcı adı veya şifre hatalı!';
	}
	else
	{
		$_SESSION['UserName'] = $u;
		$_SESSION['UserId']	  = $uid;
		$_SESSION['PassWord'] = $p;
		$ut = qa(q("SELECT TOP 1 UserID FROM {$dbtank}.dbo.Sys_Users_Detail WHERE UserName = '{$u}'")); $_SESSION['UserIdTank'] = $ut ? $ut['UserID'] : 0;
		$_SESSION['Coin']	= loadCoin($uid);
		$_SESSION['IsVip'] = IsVipUser($uid);
		$q = q("SELECT TOP 1 NickName FROM {$dbtank}.dbo.Sys_Users_Detail Where UserName = '{$u}'");
		$info = qa($q);
		$_SESSION['NickName'] = $info['NickName'];
		if($_SESSION['IsVip'] == 1) include('ItemForVipUser.php');
		echo '<script type="text/javascript">window.location="index.php";</script>';
		exit();
	}
}
if(isset($_POST['register'])) {
	$u = addslashes($_POST['rusername']);
	$p = $_POST['rpassword'];
	$rp = $_POST['rtpassword'];
	$n = addslashes($_POST['nickname']);
	$p2 = $_POST['PassTwo'];
	$e = $_POST['email'];
	$s = (int)$_POST['sex'];
	$capt = $_POST['captcha'];
	$text_r = null;
	if($u == null || $p == null || $rp == null || $n == null || $p2 == null || $e == null) {
		$text_r .= 'Tüm bilgileri doldurun. <br>';
	}
	if($capt != $_SESSION['dnss_code'])
		$text_r .= 'Güvenlik doğrulamasını hatalı girdiniz. <br>';
	if(!preg_match("/^([a-zA-Z0-9\-\_]*)$/",$u) || !preg_match("/^([a-zA-Z0-9\-\_]*)$/",$n)) {
		$text_r .= 'Kullanıcı Adı veya Karakter Adı geçersiz.<br>';
	}
	if(!filter_var($e,FILTER_VALIDATE_EMAIL)) $text_r .= ' E-posta adresinizi kontrol edin.<br>';
	if($p != $rp) $text_r.= 'Geçerli bir şifre girin. <br>';
	if(strlen($u)  < 6 || strlen($u)  > 30) $text_r .= 'Kullanıcı adı 6-30 karakter aralığında olmalıdır. <br>';
	if(strlen($p)  < 6 || strlen($p)  > 30) $text_r .= 'Şifre 6-30 karakter aralığında olmalıdır. <br>';
	if(strlen($p2) < 6 || strlen($p2) > 30) $text_r .= 'WebShop şifresi 6-30 karakter aralığında olmalıdır. <br>';
	if(strlen($n)  < 6 || strlen($n)  > 30) $text_r .= 'Karakter Adı 6-30 karakter aralığında olmalıdır. <br>';
	if($text_r == '') {
		co();
		$p = strtoupper(md5($p));
		$q = q("Select TOP 1 UserId From Mem_Users Where UserName = '{$u}'");
		if(qn($q) == 0) {
			$q = q("Select TOP 1 UserId From Webshop_Account Where Email = '{$e}'");
			if(qn($q) == 0) {
				$q = q("Select TOP 1 UserId From ".$dbtank.".dbo.Sys_Users_Detail Where NickName = '{$n}'");
				if(qn($q) == 0) {
					q("exec ".$config['Database'].".dbo.Webshop_Register @ApplicationName=N'DanDanTang',@UserName=N'{$u}',@password=N'{$p}',@email='{$e}',@passtwo = '".strtoupper(md5($p2))."',@error = 0");
					q("exec ".$dbtank.".dbo.SP_Users_Active @UserID='',@Attack=0,@Colors=N',,,,,,',@ConsortiaID=0,@Defence=0,@Gold=0,@GP=264058,@Grade=17,@Luck=0,@Money=0,@Style=N',,,,,,',@Agility=0,@State=0,@UserName=N'{$u}',@PassWord=N'{$p}',@Sex='".$s."',@Hide=1111111111,@ActiveIP=N'',@Skin=N'',@Site=N''");
					if($s == 1) {
						q("exec ".$dbtank.".dbo.SP_Users_RegisterNotValidate @UserName=N'".$u."',@PassWord=N'{$p}',@NickName=N'{$n}',@BArmID=7010,@BHairID=3158,@BFaceID=6103,@BClothID=5160,@BHatID=1142,@GArmID=7010,@GHairID=3158,@GFaceID=6103,@GClothID=5160,@GHatID=1142,@ArmColor=N'',@HairColor=N'',@FaceColor=N'',@ClothColor=N'',@HatColor=N'',@Sex='{$s}',@StyleDate=0");
					}
					else {
						q ("exec ".$dbtank.".dbo.SP_Users_RegisterNotValidate @UserName=N'{$u}',@PassWord=N'{$p}',@NickName=N'{$n}',@BArmID=7010,@BHairID=3244,@BFaceID=6204,@BClothID=5276,@BHatID=1214,@GArmID=7010,@GHairID=3244,@GFaceID=6202,@GClothID=5276,@GHatID=1214,@ArmColor=N'',@HairColor=N'',@FaceColor=N'',@ClothColor=N'',@HatColor=N'',@Sex='{$s}',@StyleDate=0");
					}
					q("exec ".$dbtank.".dbo.SP_Users_LoginWeb @UserName=N'{$u}',@Password=N'',@FirstValidate=0,@NickName=N'{$n}'");
					echo '<script type="text/javascript">alert("BBMTank\'a hoşgeldin! Hemen giriş yap, eğlenceye ilk adımını at!");</script>';
				} else $text_r .= 'Bu karakter adı zaten kullanılıyor';
			} else $text_r .= 'Bu e-posta zaten kullanılıyor';
		} else $text_r .= 'Bu kullanıcı adı zaten kullanılıyor';
	}
}
?>
<?php
/* --- canli veriler (uydurma yok, DB'den) --- */
$sv_oyuncu = 0; $sv_birlik = 0; $sv_kesif = 16; $sv_harita = 0; $sv_lider = array();
if (function_exists('co')) {
  co();
  $r = qa(q("SELECT COUNT(*) AS a FROM {$dbtank}.dbo.Sys_Users_Detail"));           if ($r) $sv_oyuncu = (int)$r['a'];
  $r = qa(q("SELECT COUNT(*) AS a FROM {$dbtank}.dbo.Consortia"));                  if ($r) $sv_birlik = (int)$r['a'];
  $r = qa(q("SELECT COUNT(*) AS a FROM {$dbtank}.dbo.Game_Map"));                   if ($r) $sv_harita = (int)$r['a'];
  $rs = q("SELECT TOP 8 NickName, Grade, Offer FROM {$dbtank}.dbo.Sys_Users_Detail
           WHERE UserName NOT LIKE '336bot%' AND UserName NOT LIKE 'botchar%'
           ORDER BY Grade DESC, Offer DESC");
  if ($rs) while ($x = qa($rs)) $sv_lider[] = $x;
}
$sayfaBaslik   = 'Ana Sayfa';
$sayfaAciklama = 'Olympos\'un tepesinde sıra tabanlı topçu savaşları. Açıyı ayarla, gücü tuttur, rüzgârı hesapla. Kurulum yok, tarayıcıdan oyna.';
include('parts/ust.php');
?>

<!-- ================= HERO ================= -->
<section class="oly-hero">
  <div class="sahne" style="background-image:url('assets/olympos/hero.jpg')"></div>
  <div class="karart"></div>
  <div class="ic ikili">

    <div style="position:relative">
      <span class="yazi-perde"></span>
      <h1 class="slogan">Olympos'un tepesinde<br>ilk atışı sen yap.</h1>
      <p class="alt-yazi">
        Sıra tabanlı topçu savaşı. Açıyı ayarla, gücü tuttur, rüzgârı hesaba kat.
        Işınlanma yok, hile yok, indirme yok. Tarayıcıyı aç, gir, ateş et.
      </p>
      <div class="dugmeler">
        <a class="btn" href="#giris">Savaşa Katıl</a>
        <a class="btn mavi" href="rehber.php">Önce Rehbere Bak</a>
      </div>
      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:22px">
        <span class="rozet">Kurulum yok</span>
        <span class="rozet mavi">Tamamen Türkçe</span>
        <span class="rozet mavi">Oynaması ücretsiz</span>
      </div>
    </div>

    <div class="giris-levha" id="giris">
      <h3>Tanrılar seni bekliyor</h3>
      <p class="not">Hesabına gir, doğruca meydana düş.</p>
      <?php if ($loginError): ?><div class="hata"><?php echo htmlspecialchars($loginError); ?></div><?php endif; ?>
      <?php if (isset($text_r) && $text_r): ?><div class="hata"><?php echo $text_r; ?></div><?php endif; ?>
      <form action="Anasayfa.php" method="POST" autocomplete="off">
        <div class="alan">
          <label for="k_ad">Kullanıcı Adı</label>
          <input id="k_ad" type="text" name="username" placeholder="kullanıcı adın" required>
        </div>
        <div class="alan">
          <label for="k_sifre">Şifre</label>
          <input id="k_sifre" type="password" name="password" placeholder="••••••••" required>
        </div>
        <button class="btn" type="submit" name="login">Giriş Yap</button>
      </form>
      <div class="baglar">
        Hesabın yok mu? <a href="register.php">Ücretsiz kayıt ol</a><br>
        <a href="getnewpass.php">Şifremi unuttum</a>
      </div>
    </div>

  </div>
</section>

<!-- ================= CANLI SAYAÇLAR ================= -->
<div class="serit">
  <div class="wrap">
    <div class="ic">
      <div class="kutu"><b data-say="<?php echo $sv_oyuncu; ?>">0</b><span>Kayıtlı Savaşçı</span></div>
      <div class="kutu"><b data-say="<?php echo $sv_harita; ?>">0</b><span>Savaş Haritası</span></div>
      <div class="kutu"><b data-say="<?php echo $sv_kesif; ?>">0</b><span>Keşif Bölgesi</span></div>
      <div class="kutu"><b data-say="<?php echo $sv_birlik; ?>">0</b><span>Kurulu Birlik</span></div>
    </div>
  </div>
</div>

<!-- ================= 3 ADIMDA BAŞLA ================= -->
<section class="bolum">
  <div class="wrap">
    <div class="baslik rv">
      <div class="tepe">Üç adım, iki dakika</div>
      <h2>Kahraman olmak <span class="vurgu">bu kadar basit</span></h2>
      <p>Kurulum dosyası indirmiyorsun, sürüm uyuşmazlığıyla uğraşmıyorsun. Kayıt ol, gir, ateş et.</p>
    </div>
    <div class="izgara i3">
      <div class="levha adim rv">
        <div class="no">I</div>
        <h3>Hesabını Kur</h3>
        <p>Kullanıcı adı, şifre, karakter adı. E-posta doğrulaması yok, bekleme yok. Otuz saniyede meydandasın.</p>
      </div>
      <div class="levha adim rv">
        <div class="no">II</div>
        <h3>Tarayıcıdan Gir</h3>
        <p>Oyun doğrudan tarayıcıda açılır. Flash içerik gömülü çalıştığı için ayrı eklenti derdi çekmezsin; takılırsan rehberde adım adım yazıyor.</p>
      </div>
      <div class="levha adim rv">
        <div class="no">III</div>
        <h3>İlk Atışını Yap</h3>
        <p>Açı, güç, rüzgâr. Üç değişken, sonsuz ihtimal. İlk isabetten sonra bırakamayacaksın, uyardık.</p>
      </div>
    </div>
  </div>
</section>

<!-- ================= NE VAR NE YOK ================= -->
<section class="bolum" id="sistemler">
  <div class="wrap">
    <div class="baslik rv">
      <div class="tepe">Sunucuda neler var</div>
      <h2>Sadece savaş <span class="vurgu">değil</span></h2>
      <p>Levhaların üstüne tıkla, içinde ne olduğunu anlatalım.</p>
    </div>
    <div class="izgara i3">

      <div class="levha tiklanir rv" data-modal
           data-baslik="Sıra Tabanlı Savaş"
           data-ust='<span class="rozet">Çekirdek sistem</span>'
           data-govde="<p>Sırası gelen atar, diğerleri bekler. Açını ok tuşlarıyla ayarlarsın, gücü boşluk tuşunu basılı tutarak biriktirirsin.</p><p><b>Kısayol:</b> güç barına tıklayıp işaret koy, sonra <b>M</b> tuşuna bas. Güç o işarete kadar kendi kendine dolar, senin tempo tutturman gerekmez.</p><p>Rüzgâr her turda değişir. Arkadan esiyorsa gücü kıs, karşıdan esiyorsa aç. Yanlış hesap mermiyi haritanın dışına yollar.</p>">
        <span class="flama">Çekirdek</span>
        <div class="ikon">🎯</div>
        <h3>Sıra Tabanlı Savaş</h3>
        <p>Açı, güç, rüzgâr. Üç değişkeni tutturan kazanır. Refleks değil hesap işi.</p>
      </div>

      <div class="levha tiklanir rv" data-modal
           data-baslik="Keşif Rıhtımı"
           data-ust='<span class="rozet mavi">16 bölge</span>'
           data-govde="<p>On altı ayrı keşif bölgesi, her biri kendi haritasında ve kendi bossuyla. Kolaydan kâbusa doğru zorluk artar, drop tablosu da öyle.</p><p>Enerji harcayarak girersin. Bossu devirince kart açma hakkı kazanırsın; kaç kart açtıysan o kadar ödül düşer.</p><p>Aynı keşfe yeterince girersen o keşfin kilidi <b>Otomatik Av</b> için açılır.</p>">
        <span class="flama">16 bölge</span>
        <div class="ikon">🗺️</div>
        <h3>Keşif Rıhtımı</h3>
        <p>On altı bölge, on altı boss, on altı ayrı drop tablosu. Her birinin kendi haritası var.</p>
      </div>

      <div class="levha tiklanir rv" data-modal
           data-baslik="Otomatik Av"
           data-ust='<span class="rozet">Sen uyurken</span>'
           data-govde="<p>Bir keşfi yeterince geçtiysen kilidi açılır ve o keşfi otomatik ava bırakabilirsin.</p><p><b>Kayıt eli:</b> keşfe <u>tek başına</u> girip bitirdiğinde geçen süre kaydedilir. Otomatik av o kayıtlı süreyi kullanarak el el ilerler.</p><p>Enerji harcar, ödülleri normal keşifteki gibi düşer, dolan çanta postaya taşar. Gece uyurken hesap kasmanın yolu bu.</p>">
        <span class="flama altin">Yeni</span>
        <div class="ikon">🌙</div>
        <h3>Otomatik Av</h3>
        <p>Kilidi açılan keşifleri otomatik ava bırak. Sen uyu, hesap kassın.</p>
      </div>

      <div class="levha tiklanir rv" data-modal
           data-baslik="Pazar Alanı"
           data-ust='<span class="rozet mavi">Oyuncu ekonomisi</span>'
           data-govde="<p>Kendi tezgâhını kurar, elindeki eşyayı istediğin fiyata satarsın. Fiyatı sen belirlersin, alıcıyı pazar bulur.</p><p>Tezgâh kurmak için VIP olman <b>gerekmez</b>; herkesin bir tezgâh hakkı vardır. VIP seviyesi yükseldikçe aynı anda kurabileceğin tezgâh sayısı artar.</p><p>Eşyayı tezgâha sürükleyip bırakman yeterli; fiyat girip onayladığında dükkân açılır.</p>">
        <span class="flama">Ekonomi</span>
        <div class="ikon">🏺</div>
        <h3>Pazar Alanı</h3>
        <p>Tezgâhını kur, fiyatını sen koy. Alıcıyı pazar bulur. VIP şartı yok.</p>
      </div>

      <div class="levha tiklanir rv" data-modal
           data-baslik="Karakter Gelişimi"
           data-ust='<span class="rozet">Uzun soluklu</span>'
           data-govde="<p>Ekipman güçlendirme, efsun, inci takma, evcil hayvan ve binek. Her biri saldırı, savunma, çeviklik ve şansına ayrı ayrı işler.</p><p>Güçlendirme başarısız olabilir; koruma eşyası kullanmadan yüksek seviyeye zorlamak risklidir.</p><p>İnci sistemi bombum kurallarıyla birebir çalışır: her yuvaya uygun inci, her inciye uygun seviye.</p>">
        <span class="flama mavi">Derinlik</span>
        <div class="ikon">⚒️</div>
        <h3>Karakter Gelişimi</h3>
        <p>Güçlendirme, efsun, inci, pet, binek. Kasmanın sonu yok.</p>
      </div>

      <div class="levha tiklanir rv" data-modal
           data-baslik="Birlikler"
           data-ust='<span class="rozet mavi">Topluluk</span>'
           data-govde="<p>Birlik kur ya da bir birliğe katıl. Birlik seviyesi yükseldikçe üyelere buff, görev ve birlik bossu açılır.</p><p>Birlik görevleri katkı puanı verir; katkı puanı birlik dükkânında harcanır.</p><p>Tek başına oynamak mümkün ama Olympos'ta yalnız savaşan uzun yaşamaz.</p>">
        <span class="flama">Birlikte</span>
        <div class="ikon">🛡️</div>
        <h3>Birlikler</h3>
        <p>Birlik kur, buff topla, birlik bossunu devir. Yalnız savaşma.</p>
      </div>

    </div>
  </div>
</section>

<!-- ================= SIRALAMA ================= -->
<section class="bolum">
  <div class="wrap">
    <div class="baslik rv">
      <div class="tepe">Olympos zirvesi</div>
      <h2>Şu an <span class="vurgu">kim tepede?</span></h2>
      <p>Canlı sunucu verisi. Listede adını görmek istiyorsan yapılacak tek bir şey var.</p>
    </div>
    <div class="sarma rv">
      <table class="tablo">
        <thead><tr><th>#</th><th>Savaşçı</th><th>Seviye</th><th>Onur</th></tr></thead>
        <tbody>
        <?php if ($sv_lider): $i = 0; foreach ($sv_lider as $o): $i++; ?>
          <tr>
            <td class="sira"><?php echo $i; ?></td>
            <td class="isim"><?php echo htmlspecialchars($o['NickName']); ?></td>
            <td><?php echo (int)$o['Grade']; ?></td>
            <td><?php echo number_format((int)$o['Offer'], 0, ',', '.'); ?></td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="4" style="text-align:center;padding:26px">Sıralama şu an yüklenemedi.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div style="text-align:center;margin-top:26px" class="rv">
      <a class="btn mavi" href="siralama.php">Tüm Sıralamayı Gör</a>
    </div>
  </div>
</section>

<!-- ================= HABERLER ================= -->
<section class="bolum" id="haberler">
  <div class="wrap">
    <div class="baslik rv">
      <div class="tepe">Sunucudan</div>
      <h2>Haberler ve <span class="vurgu">duyurular</span></h2>
    </div>
    <div class="izgara i2">
      <div class="levha parsomen rv">
        <span class="flama">Duyuru</span>
        <h3>Son gelişmeler</h3>
        <a class="haber" href="rehber.php#otoav"><span class="tar">24.08</span> Otomatik Av açıldı: kilidini açtığın keşifleri artık gece boyunca çalıştırabilirsin.</a>
        <a class="haber" href="rehber.php#pazar"><span class="tar">23.08</span> Tezgâh kurmak için VIP şartı kaldırıldı, herkes bir tezgâh açabiliyor.</a>
        <a class="haber" href="kesifler.php"><span class="tar">22.08</span> On altı keşif on altı ayrı haritaya taşındı, çakışma sorunu bitti.</a>
        <a class="haber" href="rehber.php#savas"><span class="tar">21.08</span> Güç barına işaret koyup <b>M</b> ile otomatik doldurma eklendi.</a>
      </div>
      <div class="levha parsomen rv">
        <span class="flama mavi">Rehber</span>
        <h3>Yeni başlıyorsan</h3>
        <a class="haber" href="rehber.php#savas"><span class="tar">Temel</span> Açı, güç ve rüzgâr nasıl hesaplanır?</a>
        <a class="haber" href="rehber.php#gelisim"><span class="tar">Gelişim</span> Hangi ekipmanı önce güçlendirmeliyim?</a>
        <a class="haber" href="kesifler.php"><span class="tar">Keşif</span> Hangi keşif hangi seviyeye uygun?</a>
        <a class="haber" href="destek.php#flash"><span class="tar">Teknik</span> Oyun ekranı beyaz kalıyorsa ne yapmalı?</a>
      </div>
    </div>
  </div>
</section>

<!-- ================= ÇAĞRI ================= -->
<section class="bolum">
  <div class="wrap">
    <div class="cagri rv">
      <h2>Meydan boş değil, sıra sende</h2>
      <p>
        Hesap açmak ücretsiz, oynamak ücretsiz. Kaybedecek tek şeyin biraz uyku,
        kazanacağın şey Olympos'un zirvesi.
      </p>
      <div class="dugmeler">
        <a class="btn" href="register.php">Ücretsiz Kayıt Ol</a>
        <a class="btn mavi" href="#giris">Zaten Hesabım Var</a>
      </div>
    </div>
  </div>
</section>

<?php include('parts/alt.php'); ?>
