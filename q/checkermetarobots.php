<!DOCTYPE html>
<html>
<head>
    <title>Meta name ="robots" Result Checker.</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width" />
    <link href="/q/style.css" rel="stylesheet"/>
    <script src="/q/modernize.js"></script>
    <script src="/q/seotext.js"></script>
    <link href="/q/css.css" rel="stylesheet"/>
</head>
<body>

<?
header('Content-Type: text/html; charset=utf-8');

//вывод результата в таблицу
if (trim($_POST['textt']) != ''){
	echo "<h2>Meta name =\"robots\" Result Checker <br /></h2>";
	}
else die('Поле должно быть заполнено!');


//titles
echo
"<table border=\"2\" bordercolor=\"black\">
<tr>
	<td><b>URL</b></td>
	<td><b>Meta robots</b></td>
</tr>";
$plist = explode("\r\n",$_POST['textt']);
foreach ($plist as $arraylist) {
	echo "<tr><td>".$arraylist."</td>";
	echo "<td>"." ". metarobots($arraylist). "<br /></td></tr>";
	}
echo "</table>";

$away = getUrlData($arraylist);
echo '<pre>'; print_r($away); echo '</pre>';

function metarobots($arraylist){
    $rz1 = getmetapage($arraylist);
	//var_dump ($rz1);
	$tags = get_meta_tags($arraylist);
	$metarobots = $tags['robots'];
	return encoding1($metarobots);
}

//перекодируем description, если надо
function encoding1($metarobots){
	if (mb_detect_encoding($metarobots, 'UTF-8', true)){
	return $metarobots;
	}
	else {
	$metarobots1 = cp1251_to_utf8($metarobots);
	return $metarobots1;
	}
}

function cp1251_to_utf8 ($repcharset)  {
    $in_arr = array (
        chr(208), chr(192), chr(193), chr(194),
        chr(195), chr(196), chr(197), chr(168),
        chr(198), chr(199), chr(200), chr(201),
        chr(202), chr(203), chr(204), chr(205),
        chr(206), chr(207), chr(209), chr(210),
        chr(211), chr(212), chr(213), chr(214),
        chr(215), chr(216), chr(217), chr(218),
        chr(219), chr(220), chr(221), chr(222),
        chr(223), chr(224), chr(225), chr(226),
        chr(227), chr(228), chr(229), chr(184),
        chr(230), chr(231), chr(232), chr(233),
        chr(234), chr(235), chr(236), chr(237),
        chr(238), chr(239), chr(240), chr(241),
        chr(242), chr(243), chr(244), chr(245),
        chr(246), chr(247), chr(248), chr(249),
        chr(250), chr(251), chr(252), chr(253),
        chr(254), chr(255)
    );

    $out_arr = array (
        chr(208).chr(160), chr(208).chr(144), chr(208).chr(145),
        chr(208).chr(146), chr(208).chr(147), chr(208).chr(148),
        chr(208).chr(149), chr(208).chr(129), chr(208).chr(150),
        chr(208).chr(151), chr(208).chr(152), chr(208).chr(153),
        chr(208).chr(154), chr(208).chr(155), chr(208).chr(156),
        chr(208).chr(157), chr(208).chr(158), chr(208).chr(159),
        chr(208).chr(161), chr(208).chr(162), chr(208).chr(163),
        chr(208).chr(164), chr(208).chr(165), chr(208).chr(166),
        chr(208).chr(167), chr(208).chr(168), chr(208).chr(169),
        chr(208).chr(170), chr(208).chr(171), chr(208).chr(172),
        chr(208).chr(173), chr(208).chr(174), chr(208).chr(175),
        chr(208).chr(176), chr(208).chr(177), chr(208).chr(178),
        chr(208).chr(179), chr(208).chr(180), chr(208).chr(181),
        chr(209).chr(145), chr(208).chr(182), chr(208).chr(183),
        chr(208).chr(184), chr(208).chr(185), chr(208).chr(186),
        chr(208).chr(187), chr(208).chr(188), chr(208).chr(189),
        chr(208).chr(190), chr(208).chr(191), chr(209).chr(128),
        chr(209).chr(129), chr(209).chr(130), chr(209).chr(131),
        chr(209).chr(132), chr(209).chr(133), chr(209).chr(134),
        chr(209).chr(135), chr(209).chr(136), chr(209).chr(137),
        chr(209).chr(138), chr(209).chr(139), chr(209).chr(140),
        chr(209).chr(141), chr(209).chr(142), chr(209).chr(143)
    );

    $repcharset = str_replace($in_arr,$out_arr,$repcharset);
    return $repcharset;
}

//получить содержимое страницы
function getmetapage($nadres){
  $ch = curl_init();
  $user_agent = "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2062.124 Safari/537.36";
  curl_setopt($ch, CURLOPT_URL,$nadres);
  curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_VERBOSE, false);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  // curl_setopt($ch, CURLOPT_SSLVERSION, 3);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

  $pagemeta = curl_exec($ch);
  curl_close($ch);
  return $pagemeta;
}

?>

<br />
<form class="btn">
<input type="button" border="0" value="Назад" onClick="history.back()">
</form>&nbsp;&nbsp;&nbsp;
<!--form class="btn">
<input type="button" border="0" value="Помощь" onclick="location.href='http://www.xtoolza.info/q/help.php'">
</form-->
</body>
</html>
