<?
header('Content-Type: text/html; charset=utf-8');
$url = array('a2foton.com',
'abort-dr.com.ua',
'accord-stroy.ru',
'a-fur.com',
'algeco.ru',
'amway-dostavka24.ru',
'anime-sait.ru',
'arhivcentr.ru',
'autocontrol.ru',
'complexstroy.ru',
'conceptsound.ru',
'diplatt.com',
'dovgolittya-rodyni.com.ua',
'efca.ru',
'eurosmed.ru',
'eurovorota.net',
'festi-tour.ru',
'forladyonly.ru',
'form4concrete.ru',
'gazovik-real.ru',
'get66.ru',
'hm-net.ru',
'altpremium.ru',
'eurolyx.ru',
'ilovemum.ru',
'inautomatic.ru',
'la-med.ru',
'lira-print.ru',
'lombard2000.ru',
'lssport.ru',
'mosvipremont.ru',
'multifin.ru',
'news.vesga.ru',
'obuv-opt-nsk.ru',
'otdohnites.com',
'pro-cosmetik.ru',
'rukuxni.ru',
'rus-prokat.ru',
'sportcover.ru',
'stroy-legko.ru',
'stroyprofy.net',
'styagkapola.ru',
'supertabak.ru',
'texsklad.ru',
'tkyul.ru',
'topshouse.ru',
'vaga007.ru',
'vendagroup.ru',
'versallis.ru',
'vipcostumchik.ru',
'vipstal.ru',
'wmalliance.ru',
'www.cruzak.ru',
'www.esd.su',
'www.guestworkers.ru',
'www.kvartiraprosto.ru',
'www.mebmetall.ru',
'www.slon-tea.ru',
'www.tisa.su',
'yic-mfp.ru',
'все-заборы.рф',
'грузапчасть.рф',
'красиммного.рф',
'пестовский-плотник.рф',
'цветофф.рф'
);



function result($url) 
{

foreach ($url as $val)
{
	$answer = check_http_status($val);
	$log .= $val . " " . $answer . PHP_EOL;
	if ($answer == 200 || $answer == 301)
		echo '<br>Site http://'.$val.' is avaliable.', PHP_EOL;
	else
	{
		if ($answer == 28) 
		{
			echo '<br>Resource '.$val.' is not responding. Time out operation (more than 10 sec)'. PHP_EOL;
			// Sending notification
			$from = "sitechecker@xtoolza.info";
			$to = "<gennadiy.shershov@ingate.ru>";
			$subject = "[TEST]Resource is not available";
			$message = "Resource http://$val is not available.\r\nHTTP status: $answer : Time out - слишком долгий ответ сервера (Более 10 секунд)
			\r\n http://curl.haxx.se/libcurl/c/libcurl-errors.html - справка по статусам" ;
			mail($to, $subject, $message,"Content-type:text/plain; charset = UTF-8\r\nFrom:$from");
 
		}
		
		elseif ($answer == 6) 
		{
			echo '<br>Resource '.$val.' is not responding. Time out operation (more than 10 sec)'. PHP_EOL;
			//	Send notification
			$from = "sitechecker@xtoolza.info";
			$to = "<gennadiy.shershov@ingate.ru>";
			$subject = "[TEST]Resource is not available";
			$message = "Resource http://$val is not available.\r\nHTTP status: $answer : сайт не отнесен ни к одной ns-записи. Ресурс не резолвится.
			\r\n http://curl.haxx.se/libcurl/c/libcurl-errors.html - справка по статусам" ;
			mail($to, $subject, $message,"Content-type:text/plain; charset = UTF-8\r\nFrom:$from");
 
		}
		
		else
		{
			echo '<br>Resource '.$val.' is not avaliable. Reason: '.$answer.'. ', PHP_EOL;
			// Sending notification
			$from = "sitechecker@xtoolza.info";
			$to = "<gennadiy.shershov@ingate.ru>";
			$subject = "[TEST]Resource is not available";
			$message = "Resource http://$val is not available.\r\nHTTP status: $answer \r\n http://curl.haxx.se/libcurl/c/libcurl-errors.html - справка по статусам";
			mail($to, $subject, $message,"Content-type:text/plain; charset = UTF-8\r\nFrom:$from");
		}
	}
}
  return $log . '<br>';
}

$loger = result($url);

//логировать в файл
function mylog($data){
    $data = date('[Y-m-d H:i:s] - ') . $data . PHP_EOL;
    file_put_contents('mylog.txt', $data, FILE_APPEND);
}
mylog($loger);
 
//error_log((result($url)), 3, dirname($_SERVER['SCRIPT_FILENAME']).'/mylog.log');
 
function check_http_status($url)
{
	$user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:16.0) Gecko/20100101 Firefox/16.0';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_VERBOSE, false);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($temp, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($temp, CURLOPT_SSLVERSION, 3);
    curl_setopt($temp, CURLOPT_SSL_VERIFYHOST, false);
	$page = curl_exec($ch);
 
	$error = curl_errno($ch);
	if (!empty($error))
		return $error;
 
	return ($httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE));
	curl_close($ch);
}

echo result($url);

echo "<br><br><a href=\"http://curl.haxx.se/libcurl/c/libcurl-errors.html\">http://curl.haxx.se/libcurl/c/libcurl-errors.html</a> - look reasons here";
//echo phpinfo();

?>
<?php 
/*
//проверка работает ли mail()
if (mail("gennadiy.shershov@ingate.ru", "the subject", "Example message",   
"From: webmaster@xtoolza.info \r\n")) { 
    echo "<br>messege accepted for delivery"; 
} else { 
    echo "<br>some error happen"; 
} */
?> 


