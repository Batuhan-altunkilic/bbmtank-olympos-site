<?php
function co()
{
	global $conn,$config,$c_host;
	$conn = sqlsrv_connect($c_host, $config);
	if(!$conn) die('Can\'t connect sql server, please contact admin');
}
function q($q)
{
	global $conn;
	return sqlsrv_query($conn,$q,array(),array("Scrollable"=>SQLSRV_CURSOR_KEYSET));
}
function qn($q)
{
	return @sqlsrv_num_rows($q);
}
function qa($q)
{
	return sqlsrv_fetch_array($q,SQLSRV_FETCH_ASSOC);
}
function qc()
{
	global $conn;
	sqlsrv_close($conn);
}
function geraString($tamanho = 8,$miniscula = true, $maiusculas = true, $numeros = true, $simbolos = false)
{
// Caracteres de cada tipo
$lmin = 'abcdefghijklmnopqrstuvwxyz';
$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
/* 24.08.2026: burada rakam kumesi olmasi gerekirken SQL sifresi yapistirilmisti.
   Hem sizinti hem hata: uretilen rastgele dizeler sifrenin harflerini tasiyordu.
   Dogrusu asagidaki rakam kumesi. */
$num = '0123456789';
$simb = '!@#$%*-';
// Variáveis internas
$retorno = '';
$caracteres = '';
// Agrupamos todos os caracteres que poderão ser utilizados
if ($minuscula) $caracteres .= $lmin;
if ($maiusculas) $caracteres .= $lmai;
if ($numeros) $caracteres .= $num;
if ($simbolos) $caracteres .= $simb;
// Calculamos o total de caracteres possíveis
$len = strlen($caracteres);
for ($n = 1; $n <= $tamanho; $n++) {
// Criamos um número aleatório de 1 até $len para pegar um dos caracteres
$rand = mt_rand(1, $len);
// Concatenamos um dos caracteres na variável $retorno
$retorno .= $caracteres[$rand-1];
}
return $retorno;
}
function cryptJs($script,$tipo){

		if($tipo==0){//none
			$encoding=0;
		}
		if($tipo==1){//numeric
			$encoding=10;
		}
		if($tipo==2){//normal
			$encoding=62;
		}
		if($tipo==3){// high ASCI
			$encoding=95;
		}
  if (get_magic_quotes_gpc())
    $script = stripslashes($script);
  //$encoding = 62;
  $fast_decode = isset($_POST['fast_decode']) && $_POST['fast_decode'];
  $special_char = isset($_POST['special_char'])&& $_POST['special_char'];
  
  require 'jsCrypt/class.JavaScriptPacker.php';
  $t1 = microtime(true);
  $packer = new JavaScriptPacker($script, $encoding, $fast_decode, $special_char);
  $packed = $packer->pack();
  $t2 = microtime(true);
  
  $originalLength = strlen($script);
  $packedLength = strlen($packed);
  $ratio =  number_format($packedLength / $originalLength, 3);
  $time = sprintf('%.4f', ($t2 - $t1) );
  
  return $packed;

}
function requisicaoApi($params, $endpoint){
    $url = "http://api.directcallsoft.com/{$endpoint}";
    $data = http_build_query($params);
    $ch =   curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $return = curl_exec($ch);
    curl_close($ch);
    // Converte os dados de JSON para ARRAY
    $dados = json_decode($return, true);
    return $dados;
}
function sendSms($SMS,$numero){ 
// CLIENT_ID que é fornecido pela DirectCall (Seu e-mail)
/* 24.08.2026: devralinan kaynakta BASKA BIR SUNUCUYA ait DirectCall SMS
   kimligi acik yaziyordu; bu fonksiyon hicbir yerden cagrilmiyor.
   Kimlik temizlendi, kullanilacaksa gizli.php'den okunmali. */
$client_id = "";
// CLIENT_SECRET que é fornecido pela DirectCall (Código recebido por SMS)
$client_secret = "";
// Faz a requisicao do access_token
$req = requisicaoApi(array('client_id'=>$client_id, 'client_secret'=>$client_secret), "request_token");
//Seta uma variavel com o access_token
$access_token = $req['access_token'];
// Enviadas via POST do nosso contato.html
// Monta a mensagem
// Array com os parametros para o envio
$data = array(
    'origem'=>"NexusTank", // Seu numero que Ã© origem
    'destino'=>$numero, // E o numero de destino
    'tipo'=>"texto",
    'access_token'=>$access_token,
    'texto'=>$SMS
);
// realiza o envio
$req_sms = requisicaoApi($data, "sms/send");
}
function get_ip_address() {
    // check for shared internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    // check for IPs passing through proxies
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // check if multiple ips exist in var
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (validate_ip($ip))
                    return $ip;
            }
        } else {
            if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED']))
        return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED']))
        return $_SERVER['HTTP_FORWARDED'];

    // return unreliable ip since all else failed
    return $_SERVER['REMOTE_ADDR'];
}

/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 */
function validate_ip($ip) {
    if (strtolower($ip) === 'unknown')
        return false;

    // generate ipv4 network address
    $ip = ip2long($ip);

    // if the ip is set and not equivalent to 255.255.255.255
    if ($ip !== false && $ip !== -1) {
        // make sure to get unsigned long representation of ip
        // due to discrepancies between 32 and 64 bit OSes and
        // signed numbers (ints default to signed in PHP)
        $ip = sprintf('%u', $ip);
        // do private network range checking
        if ($ip >= 0 && $ip <= 50331647) return false;
        if ($ip >= 167772160 && $ip <= 184549375) return false;
        if ($ip >= 2130706432 && $ip <= 2147483647) return false;
        if ($ip >= 2851995648 && $ip <= 2852061183) return false;
        if ($ip >= 2886729728 && $ip <= 2887778303) return false;
        if ($ip >= 3221225984 && $ip <= 3221226239) return false;
        if ($ip >= 3232235520 && $ip <= 3232301055) return false;
        if ($ip >= 4294967040) return false;
    }
    return true;
}
function sendmail($recipient,$name,$subject,$content,$AltBody)
{
	global $email,$senha,$nomeDDtank;
	require "PHPMailer/PHPMailerAutoload.php";
	$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->Port=587;
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = $email;                 // SMTP username
$mail->Password = $senha;                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
$mail->Priority = 1;
$mail->AddCustomHeader("X-MSMail-Priority: High");
// Not sure if Priority will also set the Importance header:
$mail->AddCustomHeader("Importance: High");
$mail->From = $email;
$mail->FromName = $nomeDDtank;
$mail->addAddress($recipient, $name);     // Add a recipient
$mail->addReplyTo($email, $nomeDDtank);

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $subject;
$mail->Body    = $content;
$mail->AltBody = $AltBody;

if(!$mail->send()) {
echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
   // echo 'Message has been sent';
}
}

function sendmailSuporte($from,$fromName,$recipient,$name,$subject,$content,$AltBody)
{
	global $email,$senha;
	require "PHPMailer/PHPMailerAutoload.php";
	$mail = new PHPMailer;
$mail->CharSet = 'UTF-8';
$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$mail->Port=587;
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = $email;                 // SMTP username
$mail->Password = $senha;                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
$mail->Priority = 1;
$mail->AddCustomHeader("X-MSMail-Priority: High");
// Not sure if Priority will also set the Importance header:
$mail->AddCustomHeader("Importance: High");
$mail->AddCustomHeader("Return-path: {$from}");
$mail->addReplyTo($from, $fromName);
$mail->From = $fromName;
$mail->FromName = $from;
$mail->addAddress($recipient, $name);     // Add a recipient


$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $subject;
$mail->Body    = $content;
$mail->AltBody = $AltBody;

if(!$mail->send()) {
 echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
   // echo 'Message has been sent';
}
}
function getdia(){
	$time = gmdate("Y-m-d H:i:s", time() + 3600*(date('0')-2));
	return $time;
}
function loadimage($image,$loaivp,$sex)
{
	switch($sex)
	{
		case 1:
			$ml = 'm';
		break;
		case 2:
			$ml = 'f';
		break;
		default:
			$ml = 'f';
		break;
	}
	switch($loaivp)
	{
		case 1:
			$link = 'equip/'.$ml.'/head/'.$image.'/icon_1.png';
		break;
		case 2:
			$link = 'equip/'.$ml.'/glass/'.$image.'/icon_1.png';
		break;
		case 3:
			$link = 'equip/'.$ml.'/hair/'.$image.'/icon_1.png';
		break;
		case 4:
			$link = 'virtual/m/eff/'.$image.'/icon_1.png';
		break;
		case 5:
			$link = 'equip/'.$ml.'/cloth/'.$image.'/icon_1.png';
		break;
		case 6:
			$link = 'equip/'.$ml.'/face/'.$image.'/icon_1.png';
		break;
		case 7:
			$link = 'arm/'.$image.'/00.png';
		break;
		case 8:
			$link = 'equip/armlet/'.$image.'/icon.png';
		break;
		case 9:
			$link = 'equip/ring/'.$image.'/icon.png';
		break;
		case 11:
			$link = 'unfrightprop/'.$image.'/icon.png';
		break;
		case 13:
			$link = 'equip/'.$ml.'/suits/'.$image.'/icon_1.png';
		break;
		case 15:
			$link = 'equip/wing/'.$image.'/icon.png';
		break;
		case 14:
			$link = 'necklace/'.$image.'/icon.png';
		break;
		case 17;
			$link = 'equip/offhand/'.$image.'/icon.png';
		break;
		case 16;
			$link = 'specialprop/chatBall/'.$image.'/icon.png';
		break;
		case 19;
			$link = 'prop/'.$image.'/icon.png';
		break;
		case 20;
			$link = 'prop/'.$image.'/icon.png';
		break;
		case 35;
			$link = 'unfrightprop/'.$image.'/icon.png';
		break;
		case 34;
			$link = 'unfrightprop/'.$image.'/icon.png';
		break;
		case 50;
			$link = 'petequip/arm/'.$image.'/icon.png';
		break;
		case 52;
			$link = 'petequip/cloth/'.$image.'/icon.png';
		break;
		case 51;
			$link = 'petequip/hat/'.$image.'/icon.png';
		break;
		case 61:
			$link = 'unfrightprop/'.$image.'/icon.png';
		break;
		case 62:
			$link = 'unfrightprop/'.$image.'/icon.png';
		break;
		case 80:
			$link = 'unfrightprop/'.$image.'/icon.png';
		break;
		default:
			$link = 'unfrightprop/'.$image.'/icon.png';
		break;
	}
	return $link;
}
function GetNameItem($id)
{
	$name = 'Không xác định';
	switch($id) {
	case 1:
	$name = 'Nón';
	break;
	case 2:
	$name = 'Kính';
	break;
	case 3:
	$name = 'Tóc';
	break;
	case 4:
	$name = 'Mặt';
	break;
	case 5:
	$name = 'Áo';
	break;
	case 6:
	$name = 'Mắt';
	break;
	case 7:
	$name = 'Vũ khí';
	break;
	case 8:
	$name = 'Vòng tay';
	break;
	case 9:
	$name = 'Nhẫn';
	break;
	case 13:
	$name = 'Set quần áo';
	break;
	case 15:
	$name = 'Cánh';
	break;
	case 17:
	$name = 'Vũ khí phụ';
	break;
	case 14:
	$name = 'Dây truyền';
	break;
	case 16:
	$name = 'Bong bóng';
	break;
	case 18:
	$name = 'Hộp thẻ';
	break;
	case 26:
	$name = 'Thẻ bài';
	break;
	case 25:
	$name = 'Quà tặng';
	break;
	case 12:
	$name = 'Item nhiệm vụ';
	break;
	case 11:
	$name = 'Đạo cụ hỗ trợ';
	break;
	case 19:
	$name = 'Hỗ trợ';
	break;
	case 20:
	$name = 'Nước tu luyện';
	break;
	case 23:
	$name = 'Sách tu luyện';
	break;
	case 25:
	$name = 'Quà tặng';
	break;
	case 27:
	$name = 'Vũ khí đặc biệt';
	break;
	case 30:
	$name = 'Đạo cụ đặc biệt';
	break;
	case 31:
	$name = 'Vũ khí phụ đặc biệt';
	break;
	case 32:
	$name = 'Hạt giống';
	break;
	case 33:
	$name = 'Phân hóa học';
	break;
	case 34:
	$name = 'Thực phẩm';
	break;
	case 35:
	$name = 'Trứng thú cưng';
	break;
	case 36:
	$name = 'Cây trồng';
	break;
	case 52:
	$name = 'Giáp thú cưng';
	break;
	case 51:
	$name = 'Nón thú cưng';
	break;
	case 50:
	$name = 'Vũ khí thú cưng';
	break;
	case 40:
	$name = 'Huy hiệu';
	break;
	case 60:
	$name = 'Chiến hồn';
	break;
	}
	return $name;
}
function getQualityColor($id)
{
	$color = '000000';
	switch($id) {
		case 1:
			$color = 'FFFFFF';
		break;
		case 2:
			$color = '00FF00';
		break;
		case 3:
			$color = '0066FF';
		break;
		case 4:
			$color = 'FF00FF';
		break;
		case 5:
			$color = 'FF9900';
		break;
	}
	return $color;
}
function getQualityName($id)
{
	$name = 'Không xác định';
	switch($id) {
		case 1:
			$name = 'Thô';
		break;
		case 2:
			$name = 'Thường';
		break;
		case 3:
			$name = 'Ưu';
		break;
		case 4:
			$name = 'Tinh Anh';
		break;
		case 5:
			$name = 'Xuất Sắc';
		break;
	}
	return $name;
}
function loadCoin($uid)
{
	global $conn,$dbtank;
	$q = q("Select TOP 1 Coin from {$dbtank}.dbo.Sys_Users_Detail where UserId = '{$_SESSION['UserIdTank']}'");
	$r = qa($q);
	return (int)$r['Coin'];
}
function IsVipUser($id)
{
	if(GetDayVip($id) > 0) return 1;
	return 0;
}
function GetDayVip($id)
{
	global $conn;
	$q = q("Select TOP 1 IsVip,TimeVip From Webshop_Account Where UserId = '{$id}'");
	$r = qa($q);
	if($r['TimeVip'] > time()) return ceil(($r['TimeVip']-time())/86400);
	if($r['IsVip'] == true) q("Update Webshop_Account SET IsVip = 'False' Where UserId = '{$id}'");
	return 0;
}
function GetTimeOnline() {
	global $conn,$dbtank;
	$q = q("Select Top 1 OnlineTime from {$dbtank}.dbo.Sys_Users_Detail Where UserName = '".$_SESSION['UserName']."' AND IsExist = 'True'");
	$timeonline = 0;
	if(qn($q) != 0)
	{
		$info = qa($q);
		$timeonline = floor(($info['OnlineTime'])/60);
	}
	return $timeonline;
}
function simpleLoadURL($url)
{
$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return (String)($data);
}

function GetPlayersOnline() {
global $linksite;
							$serverlist = (simpleLoadURL(''.$linksite.'/req/serverlist.ashx?rnd='.geraString(9,true,true,false).''));
							$arrayServer=explode('=',$serverlist);
							//print_r($arrayServer);
							preg_match_all('!\d+!', $arrayServer[3], $onlines);
							//$onlines = substr($serverlist, strpos($serverlist, "Online=") + 1); 
							return ($onlines[0][0]);
}
function GetPlayersOnlineServidor($serverId) {
global $linksite;
							$serverlist = (simpleLoadURL(''.$linksite.'/req/serverlist.ashx?rnd='.geraString(9,true,true,false).''));
							$arrayServer=explode('=',$serverlist);
							//print_r($arrayServer);
							if($serverId==1){
							preg_match_all('!\d+!', $arrayServer[13], $onlines);
							}
							if($serverId==2){
							preg_match_all('!\d+!', $arrayServer[22], $onlines);
							}
							//$onlines = substr($serverlist, strpos($serverlist, "Online=") + 1); 
							return ($onlines[0][0]);
}
function LogShop($data,$type) {
	global $conn;
	# 1: Auto item for vip
	# 2: Log change time online to coin
	# 3: Buy item
	# 4: Del pet
	q("INSERT INTO WebShop_Log Values ('".$_SESSION['UserId']."','".date("Y-m-d H:i:s", time())."','{$data}','{$type}')");
}
function EscapeString($string)
{
	$string = str_replace("'",'',$string);
	$string = str_replace('"','',$string);
	$string = str_replace(';','',$string);
	$string = str_replace('-','',$string);
	$string = str_replace(')','',$string);
	$string = str_replace('(','',$string);
	return $string;
}